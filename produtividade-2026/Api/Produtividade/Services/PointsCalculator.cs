using Microsoft.EntityFrameworkCore;
using Api.Produtividade.Data;
using Api.Produtividade.Models;

namespace Api.Produtividade.Services;

public class PointsCalculator
{
    private readonly ProdutividadeDbContext _dbContext;

    public PointsCalculator(ProdutividadeDbContext dbContext)
    {
        _dbContext = dbContext;
    }

    public async Task RecalculateForPeriodAsync(int fiscalId, DateTime periodStart)
    {
        var periodEnd = periodStart.AddMonths(1).AddTicks(-1);

        var activities = await _dbContext.FiscalActivities
            .Include(activity => activity.Activity)
            .ThenInclude(activity => activity.ActivityType)
            .Where(activity => activity.FiscalId == fiscalId)
            .Where(activity => activity.DeletedAt == null)
            .Where(activity => activity.ValidatedAt != null)
            .Where(activity => activity.CompletedAt >= periodStart && activity.CompletedAt <= periodEnd)
            .ToListAsync();

        var ledgers = new List<FiscalActivityLedger>();

        var ufespActivities = activities
            .Where(activity => activity.Activity.ActivityType.CalculationType == ActivityCalculationType.Ufesp)
            .GroupBy(activity => activity.ActivityId)
            .ToList();

        foreach (var group in ufespActivities)
        {
            var totalValue = group.Sum(item => item.Value ?? 0);
            var first = group.First();
            var year = first.CompletedAt?.Year ?? periodStart.Year;

            var ufespRate = await _dbContext.UfespRates
                .AsNoTracking()
                .FirstOrDefaultAsync(rate => rate.Year == year);

            if (ufespRate == null)
            {
                continue;
            }

            var quantity = totalValue / ufespRate.Value;
            var basePoint = first.Activity.Points;
            var totalPoints = (quantity * basePoint) / 10;

            ledgers.Add(new FiscalActivityLedger
            {
                ActivityId = first.ActivityId,
                FiscalId = fiscalId,
                EffectiveDate = periodStart,
                UfespValue = ufespRate.Value,
                TotalValue = totalValue,
                BasePoint = basePoint,
                TotalPoints = totalPoints,
                Quantity = quantity
            });
        }

        var pointActivities = activities
            .Where(activity => activity.Activity.ActivityType.CalculationType != ActivityCalculationType.Ufesp)
            .GroupBy(activity => activity.ActivityId)
            .ToList();

        foreach (var group in pointActivities)
        {
            var first = group.First();
            var totalPoints = group.Sum(item => item.TotalPoints ?? 0);
            var quantity = group.Sum(item => item.Quantity ?? 0);

            ledgers.Add(new FiscalActivityLedger
            {
                ActivityId = first.ActivityId,
                FiscalId = fiscalId,
                EffectiveDate = periodStart,
                BasePoint = first.Activity.Points,
                TotalPoints = totalPoints,
                Quantity = quantity
            });
        }

        var existingLedgers = _dbContext.FiscalActivityLedgers
            .Where(ledger => ledger.FiscalId == fiscalId)
            .Where(ledger => ledger.EffectiveDate == periodStart);

        _dbContext.FiscalActivityLedgers.RemoveRange(existingLedgers);
        await _dbContext.FiscalActivityLedgers.AddRangeAsync(ledgers);

        var ufespPoints = activities
            .Where(activity => activity.Activity.ActivityType.CalculationType == ActivityCalculationType.Ufesp)
            .Sum(activity => activity.TotalPoints ?? 0);

        var pontuacaoPoints = activities
            .Where(activity => activity.Activity.ActivityType.CalculationType == ActivityCalculationType.Pontuacao)
            .Sum(activity => activity.TotalPoints ?? 0);

        var deducaoPoints = activities
            .Where(activity => activity.Activity.ActivityType.CalculationType == ActivityCalculationType.Deducao)
            .Sum(activity => activity.TotalPoints ?? 0);

        var totals = new FiscalTotalPoints
        {
            FiscalId = fiscalId,
            EffectiveDate = periodStart,
            PointsUfesp = ufespPoints,
            PointsPontuacao = pontuacaoPoints,
            PointsDeduction = deducaoPoints,
            TotalCollected = ledgers.Sum(ledger => ledger.TotalValue ?? 0)
        };

        totals.TotalPoints = totals.PointsUfesp + totals.PointsPontuacao - totals.PointsDeduction;
        totals.RemainingBalance = totals.TotalPoints;

        var existingTotals = _dbContext.FiscalTotalPoints
            .Where(total => total.FiscalId == fiscalId)
            .Where(total => total.EffectiveDate == periodStart);

        _dbContext.FiscalTotalPoints.RemoveRange(existingTotals);
        _dbContext.FiscalTotalPoints.Add(totals);

        await _dbContext.SaveChangesAsync();
    }
}
