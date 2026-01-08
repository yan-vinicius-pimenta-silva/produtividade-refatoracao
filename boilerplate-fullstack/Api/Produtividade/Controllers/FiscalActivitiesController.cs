using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using Api.Produtividade.Data;
using Api.Produtividade.Models;

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

    [HttpPost]
    public async Task<ActionResult<FiscalActivityResponse>> Create([FromBody] FiscalActivityRequest request)
    {
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
            FiscalId = request.FiscalId,
            CompanyId = request.CompanyId,
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

    public record FiscalActivityResponse
    {
        public long Id { get; init; }
        public int TotalPoints { get; init; }
        public int Quantity { get; init; }
    }
}
