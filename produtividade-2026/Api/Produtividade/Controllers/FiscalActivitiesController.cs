using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using Api.Produtividade.Data;
using Api.Produtividade.Models;
using Api.Helpers;
using System.Security.Claims;

namespace Api.Produtividade.Controllers;

[ApiController]
[Route("api/produtividade/fiscal-activities")]
public class FiscalActivitiesController : ControllerBase
{
    private readonly ProdutividadeDbContext _dbContext;

    public FiscalActivitiesController(ProdutividadeDbContext dbContext)
    {
        _dbContext = dbContext;
    }

    [HttpGet]
    public async Task<ActionResult<List<FiscalActivitySummary>>> List(
        [FromQuery] int? companyId,
        [FromQuery] bool? validated)
    {
        var query = _dbContext.FiscalActivities
            .AsNoTracking()
            .Include(activity => activity.Activity)
            .ThenInclude(activity => activity.ActivityType)
            .Include(activity => activity.Fiscal)
            .Where(activity => activity.DeletedAt == null);

        if (companyId.HasValue)
        {
            query = query.Where(activity => activity.CompanyId == companyId.Value);
        }

        if (validated.HasValue)
        {
            query = validated.Value
                ? query.Where(activity => activity.ValidatedAt != null)
                : query.Where(activity => activity.ValidatedAt == null);
        }

        var results = await query
            .OrderByDescending(activity => activity.CompletedAt)
            .Select(activity => new FiscalActivitySummary
            {
                Id = activity.Id,
                ActivityId = activity.ActivityId,
                ActivityName = activity.Activity.Description,
                CalculationType = activity.Activity.ActivityType.CalculationType,
                CompletedAt = activity.CompletedAt,
                Protocol = activity.Protocol,
                Document = activity.Document,
                Rc = activity.Rc,
                CpfCnpj = activity.CpfCnpj,
                TotalPoints = activity.TotalPoints,
                Quantity = activity.Quantity,
                Value = activity.Value,
                FiscalName = activity.Fiscal.Name,
                Notes = activity.Notes,
                ValidatedAt = activity.ValidatedAt
            })
            .ToListAsync();

        return Ok(results);
    }

    [HttpPost]
    public async Task<ActionResult<FiscalActivityResponse>> Create([FromBody] FiscalActivityRequest request)
    {
        var fiscalId = request.FiscalId;
        var companyId = request.CompanyId;

        if (fiscalId == 0 || companyId == 0)
        {
            var tokenPayload = GetTokenPayload();
            fiscalId = fiscalId == 0 ? tokenPayload.FiscalId : fiscalId;
            companyId = companyId == 0 ? tokenPayload.CompanyId : companyId;
        }

        if (fiscalId == 0 || companyId == 0)
        {
            return BadRequest("Fiscal e empresa são obrigatórios.");
        }

        var activity = await _dbContext.Activities
            .Include(a => a.ActivityType)
            .FirstOrDefaultAsync(a => a.Id == request.ActivityId);

        if (activity == null)
        {
            return NotFound();
        }

        var fiscalActivity = new FiscalActivity
        {
            ActivityId = activity.Id,
            FiscalId = fiscalId,
            CompanyId = companyId,
            Document = request.Document,
            Protocol = request.Protocol,
            CpfCnpj = request.CpfCnpj,
            Rc = request.Rc,
            Notes = request.Notes?.ToUpperInvariant(),
            CompletedAt = request.CompletedAt,
            CreatedAt = DateTime.UtcNow
        };

        if (activity.ActivityType.CalculationType == ActivityCalculationType.Ufesp)
        {
            if (request.Value == null)
            {
                return BadRequest("Valor do lançamento é obrigatório para UFESP.");
            }

            var year = request.CompletedAt.Year;
            var ufesp = await _dbContext.UfespRates
                .FirstOrDefaultAsync(rate => rate.Year == year && rate.IsActive);

            if (ufesp == null)
            {
                return BadRequest("Nenhuma UFESP ativa para o ano informado.");
            }

            var quantityUfesp = request.Value.Value / ufesp.Value;
            var totalPoints = (quantityUfesp * activity.Points) / 10;

            fiscalActivity.UfespYear = ufesp.Year.ToString();
            fiscalActivity.Value = request.Value;
            fiscalActivity.Quantity = quantityUfesp;
            fiscalActivity.TotalPoints = totalPoints;
        }
        else
        {
            var quantity = request.Quantity ?? 0;
            if (activity.ActivityType.CalculationType == ActivityCalculationType.Deducao && request.Quantity == null)
            {
                quantity = 10;
            }
            var totalPoints = (quantity * activity.Points) / 10;

            fiscalActivity.Quantity = quantity;
            fiscalActivity.TotalPoints = totalPoints;
        }

        foreach (var attachment in request.Attachments)
        {
            fiscalActivity.Attachments.Add(new FiscalActivityAttachment
            {
                Path = attachment
            });
        }

        _dbContext.FiscalActivities.Add(fiscalActivity);
        await _dbContext.SaveChangesAsync();

        return Ok(new FiscalActivityResponse
        {
            Id = fiscalActivity.Id,
            TotalPoints = fiscalActivity.TotalPoints ?? 0,
            Quantity = fiscalActivity.Quantity ?? 0
        });
    }

    [HttpPut("{id:long}")]
    public async Task<ActionResult<FiscalActivityResponse>> Update(long id, [FromBody] FiscalActivityUpdateRequest request)
    {
        var fiscalActivity = await _dbContext.FiscalActivities
            .Include(activity => activity.Activity)
            .ThenInclude(activity => activity.ActivityType)
            .FirstOrDefaultAsync(activity => activity.Id == id && activity.DeletedAt == null);

        if (fiscalActivity == null)
        {
            return NotFound();
        }

        var activity = fiscalActivity.Activity;
        if (request.ActivityId.HasValue && request.ActivityId.Value != fiscalActivity.ActivityId)
        {
            activity = await _dbContext.Activities
                .Include(a => a.ActivityType)
                .FirstOrDefaultAsync(a => a.Id == request.ActivityId.Value);

            if (activity == null)
            {
                return NotFound();
            }

            fiscalActivity.ActivityId = activity.Id;
        }

        fiscalActivity.Document = request.Document ?? fiscalActivity.Document;
        fiscalActivity.Protocol = request.Protocol ?? fiscalActivity.Protocol;
        fiscalActivity.CpfCnpj = request.CpfCnpj ?? fiscalActivity.CpfCnpj;
        fiscalActivity.Rc = request.Rc ?? fiscalActivity.Rc;
        fiscalActivity.Notes = request.Notes ?? fiscalActivity.Notes;
        fiscalActivity.CompletedAt = request.CompletedAt ?? fiscalActivity.CompletedAt;
        fiscalActivity.UpdatedAt = DateTime.UtcNow;

        if (activity.ActivityType.CalculationType == ActivityCalculationType.Ufesp)
        {
            var value = request.Value ?? fiscalActivity.Value;
            if (value == null)
            {
                return BadRequest("Valor do lançamento é obrigatório para UFESP.");
            }

            var year = (request.CompletedAt ?? fiscalActivity.CompletedAt)?.Year ?? DateTime.UtcNow.Year;
            var ufesp = await _dbContext.UfespRates
                .FirstOrDefaultAsync(rate => rate.Year == year && rate.IsActive);

            if (ufesp == null)
            {
                return BadRequest("Nenhuma UFESP ativa para o ano informado.");
            }

            var quantityUfesp = value.Value / ufesp.Value;
            var totalPoints = (quantityUfesp * activity.Points) / 10;

            fiscalActivity.UfespYear = ufesp.Year.ToString();
            fiscalActivity.Value = value;
            fiscalActivity.Quantity = quantityUfesp;
            fiscalActivity.TotalPoints = totalPoints;
        }
        else
        {
            var quantity = request.Quantity ?? fiscalActivity.Quantity ?? 0;
            if (activity.ActivityType.CalculationType == ActivityCalculationType.Deducao && request.Quantity == null)
            {
                quantity = 10;
            }

            var totalPoints = (quantity * activity.Points) / 10;

            fiscalActivity.Quantity = quantity;
            fiscalActivity.TotalPoints = totalPoints;
        }

        await _dbContext.SaveChangesAsync();

        return Ok(new FiscalActivityResponse
        {
            Id = fiscalActivity.Id,
            TotalPoints = fiscalActivity.TotalPoints ?? 0,
            Quantity = fiscalActivity.Quantity ?? 0
        });
    }

    [HttpPost("confirm")]
    public async Task<ActionResult<ConfirmResult>> Confirm([FromBody] ConfirmRequest request)
    {
        if (request.ActivityIds.Count == 0)
        {
            return BadRequest("Nenhuma atividade informada para confirmação.");
        }

        var activities = await _dbContext.FiscalActivities
            .Where(activity => request.ActivityIds.Contains(activity.Id) && activity.DeletedAt == null)
            .ToListAsync();

        foreach (var activity in activities)
        {
            activity.ValidatedAt = DateTime.UtcNow;
            activity.ValidatedBy = request.ValidatedBy;
        }

        await _dbContext.SaveChangesAsync();

        return Ok(new ConfirmResult(activities.Count));
    }

    [HttpDelete("{id:long}")]
    public async Task<IActionResult> Delete(long id, [FromBody] DeleteRequest? request)
    {
        var fiscalActivity = await _dbContext.FiscalActivities
            .FirstOrDefaultAsync(activity => activity.Id == id && activity.DeletedAt == null);

        if (fiscalActivity == null)
        {
            return NotFound();
        }

        fiscalActivity.DeletedAt = DateTime.UtcNow;
        fiscalActivity.DeletedBy = request?.DeletedBy;
        fiscalActivity.DeleteReason = request?.Reason;

        await _dbContext.SaveChangesAsync();

        return NoContent();
    }

    public record FiscalActivityRequest(
        int ActivityId,
        int FiscalId,
        int CompanyId,
        DateTime CompletedAt,
        string? Document,
        string? Protocol,
        string? CpfCnpj,
        string? Rc,
        int? Value,
        int? Quantity,
        string? Notes,
        List<string> Attachments);

    public record FiscalActivityUpdateRequest(
        int? ActivityId,
        DateTime? CompletedAt,
        string? Document,
        string? Protocol,
        string? CpfCnpj,
        string? Rc,
        int? Value,
        int? Quantity,
        string? Notes);

    public record ConfirmRequest(List<long> ActivityIds, string? ValidatedBy);
    public record ConfirmResult(int Updated);
    public record DeleteRequest(string? DeletedBy, string? Reason);
    private record TokenPayload(int FiscalId, int CompanyId);

    public record FiscalActivitySummary
    {
        public long Id { get; init; }
        public int ActivityId { get; init; }
        public string ActivityName { get; init; } = string.Empty;
        public ActivityCalculationType CalculationType { get; init; }
        public DateTime? CompletedAt { get; init; }
        public string? Protocol { get; init; }
        public string? Document { get; init; }
        public string? Rc { get; init; }
        public string? CpfCnpj { get; init; }
        public int? TotalPoints { get; init; }
        public int? Quantity { get; init; }
        public int? Value { get; init; }
        public string FiscalName { get; init; } = string.Empty;
        public string? Notes { get; init; }
        public DateTime? ValidatedAt { get; init; }
    }

    public record FiscalActivityResponse
    {
        public long Id { get; init; }
        public int TotalPoints { get; init; }
        public int Quantity { get; init; }
    }

    private TokenPayload GetTokenPayload()
    {
        var authHeader = HttpContext.Request.Headers["Authorization"].FirstOrDefault();
        if (string.IsNullOrWhiteSpace(authHeader) || !authHeader.StartsWith("Bearer "))
        {
            return new TokenPayload(0, 0);
        }

        var token = authHeader.Substring("Bearer ".Length).Trim();
        ClaimsPrincipal principal;
        try
        {
            principal = JsonWebToken.Decode(token);
        }
        catch
        {
            return new TokenPayload(0, 0);
        }

        var fiscalId = ParseIntClaim(principal, "id");
        var companyId = ParseIntClaim(principal, "companyId");

        return new TokenPayload(fiscalId, companyId);
    }

    private static int ParseIntClaim(ClaimsPrincipal principal, string claimType)
    {
        var value = principal.Claims.FirstOrDefault(claim => claim.Type == claimType)?.Value;
        return int.TryParse(value, out var parsed) ? parsed : 0;
    }
}
