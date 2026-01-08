using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using Produtividade.Api.Data;
using Produtividade.Api.Models;

namespace Produtividade.Api.Controllers;

[ApiController]
[Route("api/ufesp")]
public class UfespController : ControllerBase
{
    private readonly ProdutividadeDbContext _dbContext;

    public UfespController(ProdutividadeDbContext dbContext)
    {
        _dbContext = dbContext;
    }

    [HttpGet]
    public async Task<ActionResult<List<UfespRate>>> List()
    {
        return Ok(await _dbContext.UfespRates.AsNoTracking().ToListAsync());
    }

    [HttpPost]
    public async Task<ActionResult<UfespRate>> Create([FromBody] UfespRequest request)
    {
        var ufesp = new UfespRate
        {
            Year = request.Year,
            Name = request.Name,
            Value = request.Value,
            IsActive = request.IsActive
        };

        _dbContext.UfespRates.Add(ufesp);
        await _dbContext.SaveChangesAsync();

        return Ok(ufesp);
    }

    public record UfespRequest(int Year, string Name, int Value, bool IsActive);
}
