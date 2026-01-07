using Api.Data;
using Api.Dtos.Productivity;
using Api.Models;
using Microsoft.EntityFrameworkCore;

namespace Api.Services.ProductivityServices;

public class FiscalActivityService
{
    private readonly ApiDbContext _db;
    private readonly UfespRateService _ufespRateService;

    public FiscalActivityService(ApiDbContext db, UfespRateService ufespRateService)
    {
        _db = db;
        _ufespRateService = ufespRateService;
    }

    public async Task<List<FiscalActivityReadDto>> GetAllAsync(int? companyId = null, int? fiscalUserId = null)
    {
        var query = _db.FiscalActivities.AsQueryable();
        if (companyId.HasValue)
            query = query.Where(fa => fa.CompanyId == companyId.Value);
        if (fiscalUserId.HasValue)
            query = query.Where(fa => fa.FiscalUserId == fiscalUserId.Value);

        return await query
            .OrderByDescending(fa => fa.CreatedAt)
            .Select(fa => new FiscalActivityReadDto
            {
                Id = fa.Id,
                ActivityId = fa.ActivityId,
                CompanyId = fa.CompanyId,
                FiscalUserId = fa.FiscalUserId,
                DocumentNumber = fa.DocumentNumber,
                ProtocolNumber = fa.ProtocolNumber,
                Rc = fa.Rc,
                CpfCnpj = fa.CpfCnpj,
                UfespYear = fa.UfespYear,
                UfespValue = fa.UfespValue,
                Quantity = fa.Quantity,
                PointsTotal = fa.PointsTotal,
                Value = fa.Value,
                Observation = fa.Observation,
                Validated = fa.Validated,
                CompletionDate = fa.CompletionDate,
                CreatedAt = fa.CreatedAt
            })
            .ToListAsync();
    }

    public async Task<(FiscalActivityReadDto? Result, string? Error)> CreateAsync(FiscalActivityCreateDto dto)
    {
        var activity = await _db.Activities
            .Include(a => a.ActivityType)
            .FirstOrDefaultAsync(a => a.Id == dto.ActivityId);
        if (activity == null)
            return (null, "Atividade não encontrada.");

        var companyExists = await _db.Companies.AnyAsync(c => c.Id == dto.CompanyId);
        if (!companyExists)
            return (null, "Empresa não encontrada.");

        var fiscalExists = await _db.Users.AnyAsync(u => u.Id == dto.FiscalUserId);
        if (!fiscalExists)
            return (null, "Fiscal não encontrado.");

        var (quantity, pointsTotal, ufespYear, ufespValue) = await CalculatePointsAsync(activity, dto);

        var now = DateTime.UtcNow;
        var fiscalActivity = new FiscalActivity
        {
            ActivityId = dto.ActivityId,
            CompanyId = dto.CompanyId,
            FiscalUserId = dto.FiscalUserId,
            DocumentNumber = dto.DocumentNumber?.Trim(),
            ProtocolNumber = dto.ProtocolNumber?.Trim(),
            Rc = dto.Rc?.Trim(),
            CpfCnpj = dto.CpfCnpj?.Trim(),
            Value = dto.Value,
            Quantity = quantity,
            PointsTotal = pointsTotal,
            UfespYear = ufespYear,
            UfespValue = ufespValue,
            Observation = dto.Observation?.Trim(),
            CompletionDate = dto.CompletionDate,
            CreatedAt = now,
            UpdatedAt = now
        };

        _db.FiscalActivities.Add(fiscalActivity);
        await _db.SaveChangesAsync();

        return (new FiscalActivityReadDto
        {
            Id = fiscalActivity.Id,
            ActivityId = fiscalActivity.ActivityId,
            CompanyId = fiscalActivity.CompanyId,
            FiscalUserId = fiscalActivity.FiscalUserId,
            DocumentNumber = fiscalActivity.DocumentNumber,
            ProtocolNumber = fiscalActivity.ProtocolNumber,
            Rc = fiscalActivity.Rc,
            CpfCnpj = fiscalActivity.CpfCnpj,
            UfespYear = fiscalActivity.UfespYear,
            UfespValue = fiscalActivity.UfespValue,
            Quantity = fiscalActivity.Quantity,
            PointsTotal = fiscalActivity.PointsTotal,
            Value = fiscalActivity.Value,
            Observation = fiscalActivity.Observation,
            Validated = fiscalActivity.Validated,
            CompletionDate = fiscalActivity.CompletionDate,
            CreatedAt = fiscalActivity.CreatedAt
        }, null);
    }

    private async Task<(int? Quantity, int? PointsTotal, int? UfespYear, int? UfespValue)> CalculatePointsAsync(
        Activity activity,
        FiscalActivityCreateDto dto)
    {
        var activityType = activity.ActivityType?.Name.ToUpperInvariant() ?? string.Empty;

        if (activityType == "UFESP")
        {
            if (!dto.Value.HasValue)
                throw new InvalidOperationException("Valor arrecadado é obrigatório para atividades UFESP.");

            var year = dto.CompletionDate.Year;
            var ufesp = await _ufespRateService.GetActiveByYearAsync(year);
            if (ufesp == null)
                throw new InvalidOperationException("Nenhuma UFESP ativa encontrada para o ano informado.");

            var valorLancamento = Convert.ToInt32(Math.Truncate(dto.Value.Value * 100));
            var quantidadeUfesp = valorLancamento / ufesp.Value;
            var pontos = (quantidadeUfesp * activity.PointsBase) / 10;

            return (quantidadeUfesp, pontos, ufesp.Year, ufesp.Value);
        }

        var quantity = dto.Quantity ?? 10;
        var total = (quantity * activity.PointsBase) / 10;
        return (quantity, total, null, null);
    }
}
