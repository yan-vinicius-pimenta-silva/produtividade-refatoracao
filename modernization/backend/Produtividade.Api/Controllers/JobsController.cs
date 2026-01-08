using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using Produtividade.Api.Data;
using Produtividade.Api.Models;
using Produtividade.Api.Services;

namespace Produtividade.Api.Controllers;

[ApiController]
[Route("api/jobs")]
public class JobsController : ControllerBase
{
    private readonly ProdutividadeDbContext _dbContext;
    private readonly PointsCalculator _calculator;

    public JobsController(ProdutividadeDbContext dbContext, PointsCalculator calculator)
    {
        _dbContext = dbContext;
        _calculator = calculator;
    }

    [HttpPost("calculate-points")]
    public async Task<IActionResult> CalculatePoints()
    {
        var fiscals = await _dbContext.Users
            .Where(user => user.Role == UserRole.Fiscal)
            .ToListAsync();

        var periods = new[]
        {
            DateTime.UtcNow.Date.AddDays(1 - DateTime.UtcNow.Day),
            DateTime.UtcNow.Date.AddMonths(-1).AddDays(1 - DateTime.UtcNow.AddMonths(-1).Day),
            DateTime.UtcNow.Date.AddMonths(-2).AddDays(1 - DateTime.UtcNow.AddMonths(-2).Day)
        };

        foreach (var period in periods)
        {
            foreach (var fiscal in fiscals)
            {
                await _calculator.RecalculateForPeriodAsync(fiscal.Id, period);
            }
        }

        return Ok(new { status = "SUCCESS" });
    }
}
