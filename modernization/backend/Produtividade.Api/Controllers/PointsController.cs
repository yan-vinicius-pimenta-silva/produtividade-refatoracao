using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using Produtividade.Api.Data;
using Produtividade.Api.Services;

namespace Produtividade.Api.Controllers;

[ApiController]
[Route("api/points")]
public class PointsController : ControllerBase
{
    private readonly ProdutividadeDbContext _dbContext;
    private readonly PointsCalculator _calculator;

    public PointsController(ProdutividadeDbContext dbContext, PointsCalculator calculator)
    {
        _dbContext = dbContext;
        _calculator = calculator;
    }

    [HttpGet]
    public async Task<ActionResult<PointSummary>> Get([FromQuery] int fiscalId, [FromQuery] string period)
    {
        if (!DateTime.TryParse($"{period}-01", out var periodStart))
        {
            return BadRequest("Período inválido. Use YYYY-MM.");
        }

        await _calculator.RecalculateForPeriodAsync(fiscalId, periodStart);

        var totals = await _dbContext.FiscalTotalPoints
            .AsNoTracking()
            .FirstOrDefaultAsync(total => total.FiscalId == fiscalId && total.EffectiveDate == periodStart);

        if (totals == null)
        {
            return NotFound();
        }

        return Ok(new PointSummary
        {
            PointsPontuacao = totals.PointsPontuacao / 10m,
            PointsDeducao = totals.PointsDeduction / 10m,
            PointsUfesp = totals.PointsUfesp / 10m,
            PointsTotal = totals.TotalPoints / 10m,
            TotalCollected = totals.TotalCollected / 100m,
            RemainingBalance = totals.RemainingBalance / 10m
        });
    }

    public record PointSummary
    {
        public decimal PointsPontuacao { get; init; }
        public decimal PointsDeducao { get; init; }
        public decimal PointsUfesp { get; init; }
        public decimal PointsTotal { get; init; }
        public decimal TotalCollected { get; init; }
        public decimal RemainingBalance { get; init; }
    }
}
