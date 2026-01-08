using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using Api.Produtividade.Data;
using Api.Produtividade.Models;

namespace Api.Produtividade.Controllers;

[ApiController]
[Route("api/produtividade/activity-types")]
public class ActivityTypesController : ControllerBase
{
    private readonly ProdutividadeDbContext _dbContext;

    public ActivityTypesController(ProdutividadeDbContext dbContext)
    {
        _dbContext = dbContext;
    }

    [HttpGet]
    public async Task<ActionResult<List<ActivityTypeSummary>>> List()
    {
        var types = await _dbContext.ActivityTypes
            .AsNoTracking()
            .OrderBy(type => type.Name)
            .Select(type => new ActivityTypeSummary
            {
                Id = type.Id,
                Name = type.Name,
                CalculationType = type.CalculationType,
                IsActive = type.IsActive
            })
            .ToListAsync();

        return Ok(types);
    }

    public record ActivityTypeSummary
    {
        public int Id { get; init; }
        public string Name { get; init; } = string.Empty;
        public ActivityCalculationType CalculationType { get; init; }
        public bool IsActive { get; init; }
    }
}
