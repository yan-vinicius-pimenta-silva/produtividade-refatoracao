using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using Api.Helpers;
using Api.Produtividade.Data;
using Api.Produtividade.Models;
using System.Security.Claims;

namespace Api.Produtividade.Controllers;

[ApiController]
[Route("api/produtividade/activities")]
public class ActivitiesController : ControllerBase
{
    private readonly ProdutividadeDbContext _dbContext;

    public ActivitiesController(ProdutividadeDbContext dbContext)
    {
        _dbContext = dbContext;
    }

    [HttpGet]
    public async Task<ActionResult<List<ActivitySummary>>> List(
        [FromQuery] int? companyId,
        [FromQuery] bool? active)
    {
        var query = _dbContext.Activities
            .AsNoTracking()
            .Include(activity => activity.ActivityType)
            .AsQueryable();

        if (companyId.HasValue)
        {
            query = query.Where(activity => activity.CompanyId == companyId.Value);
        }

        if (active.HasValue)
        {
            query = query.Where(activity => activity.IsActive == active.Value);
        }

        var activities = await query
            .OrderBy(activity => activity.Description)
            .Select(activity => new ActivitySummary
            {
                Id = activity.Id,
                Description = activity.Description,
                Points = activity.Points,
                IsActive = activity.IsActive,
                HasMultiplicator = activity.HasMultiplicator,
                IsOsActivity = activity.IsOsActivity,
                ActivityTypeId = activity.ActivityTypeId,
                ActivityTypeName = activity.ActivityType.Name,
                CalculationType = activity.ActivityType.CalculationType,
                CompanyId = activity.CompanyId
            })
            .ToListAsync();

        return Ok(activities);
    }

    [HttpPost]
    public async Task<ActionResult<ActivitySummary>> Create([FromBody] ActivityRequest request)
    {
        var companyId = request.CompanyId;
        if (companyId == 0)
        {
            companyId = GetTokenCompanyId();
        }

        if (companyId == 0)
        {
            return BadRequest("Empresa é obrigatória.");
        }

        var activityType = await _dbContext.ActivityTypes.FirstOrDefaultAsync(type => type.Id == request.ActivityTypeId);
        if (activityType == null)
        {
            return NotFound("Tipo de atividade não encontrado.");
        }

        var activity = new Activity
        {
            Description = request.Description,
            Points = request.Points,
            IsActive = request.IsActive,
            HasMultiplicator = request.HasMultiplicator,
            IsOsActivity = request.IsOsActivity,
            ActivityTypeId = request.ActivityTypeId,
            CompanyId = companyId
        };

        _dbContext.Activities.Add(activity);
        await _dbContext.SaveChangesAsync();

        return Ok(new ActivitySummary
        {
            Id = activity.Id,
            Description = activity.Description,
            Points = activity.Points,
            IsActive = activity.IsActive,
            HasMultiplicator = activity.HasMultiplicator,
            IsOsActivity = activity.IsOsActivity,
            ActivityTypeId = activity.ActivityTypeId,
            ActivityTypeName = activityType.Name,
            CalculationType = activityType.CalculationType,
            CompanyId = activity.CompanyId
        });
    }

    [HttpPut("{id:int}")]
    public async Task<ActionResult<ActivitySummary>> Update(int id, [FromBody] ActivityRequest request)
    {
        var activity = await _dbContext.Activities
            .Include(current => current.ActivityType)
            .FirstOrDefaultAsync(current => current.Id == id);

        if (activity == null)
        {
            return NotFound();
        }

        var activityType = activity.ActivityType;
        if (activity.ActivityTypeId != request.ActivityTypeId)
        {
            activityType = await _dbContext.ActivityTypes.FirstOrDefaultAsync(type => type.Id == request.ActivityTypeId);
            if (activityType == null)
            {
                return NotFound("Tipo de atividade não encontrado.");
            }
            activity.ActivityTypeId = request.ActivityTypeId;
        }

        activity.Description = request.Description;
        activity.Points = request.Points;
        activity.IsActive = request.IsActive;
        activity.HasMultiplicator = request.HasMultiplicator;
        activity.IsOsActivity = request.IsOsActivity;

        if (request.CompanyId != 0)
        {
            activity.CompanyId = request.CompanyId;
        }

        await _dbContext.SaveChangesAsync();

        return Ok(new ActivitySummary
        {
            Id = activity.Id,
            Description = activity.Description,
            Points = activity.Points,
            IsActive = activity.IsActive,
            HasMultiplicator = activity.HasMultiplicator,
            IsOsActivity = activity.IsOsActivity,
            ActivityTypeId = activity.ActivityTypeId,
            ActivityTypeName = activityType.Name,
            CalculationType = activityType.CalculationType,
            CompanyId = activity.CompanyId
        });
    }

    [HttpDelete("{id:int}")]
    public async Task<IActionResult> Delete(int id)
    {
        var activity = await _dbContext.Activities.FirstOrDefaultAsync(current => current.Id == id);
        if (activity == null)
        {
            return NotFound();
        }

        _dbContext.Activities.Remove(activity);
        await _dbContext.SaveChangesAsync();

        return NoContent();
    }

    public record ActivityRequest(
        string Description,
        int Points,
        bool IsActive,
        bool HasMultiplicator,
        bool IsOsActivity,
        int ActivityTypeId,
        int CompanyId);

    public record ActivitySummary
    {
        public int Id { get; init; }
        public string Description { get; init; } = string.Empty;
        public int Points { get; init; }
        public bool IsActive { get; init; }
        public bool HasMultiplicator { get; init; }
        public bool IsOsActivity { get; init; }
        public int ActivityTypeId { get; init; }
        public string ActivityTypeName { get; init; } = string.Empty;
        public ActivityCalculationType CalculationType { get; init; }
        public int CompanyId { get; init; }
    }

    private int GetTokenCompanyId()
    {
        var authHeader = HttpContext.Request.Headers["Authorization"].FirstOrDefault();
        if (string.IsNullOrWhiteSpace(authHeader) || !authHeader.StartsWith("Bearer "))
        {
            return 0;
        }

        var token = authHeader.Substring("Bearer ".Length).Trim();
        ClaimsPrincipal principal;
        try
        {
            principal = JsonWebToken.Decode(token);
        }
        catch
        {
            return 0;
        }

        var value = principal.Claims.FirstOrDefault(claim => claim.Type == "companyId")?.Value;
        return int.TryParse(value, out var parsed) ? parsed : 0;
    }
}
