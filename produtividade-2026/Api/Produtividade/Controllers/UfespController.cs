using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using Api.Produtividade.Data;
using Api.Produtividade.Models;

namespace Api.Produtividade.Controllers;

[ApiController]
[Route("api/produtividade/ufesp")]
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

    [HttpPut("{year:int}")]
    public async Task<ActionResult<UfespRate>> Update(int year, [FromBody] UfespRequest request)
    {
        var ufesp = await _dbContext.UfespRates.FirstOrDefaultAsync(rate => rate.Year == year);
        if (ufesp == null)
        {
            return NotFound();
        }

        ufesp.Year = request.Year;
        ufesp.Name = request.Name;
        ufesp.Value = request.Value;
        ufesp.IsActive = request.IsActive;

        await _dbContext.SaveChangesAsync();

        return Ok(ufesp);
    }

    [HttpDelete("{year:int}")]
    public async Task<IActionResult> Delete(int year)
    {
        var ufesp = await _dbContext.UfespRates.FirstOrDefaultAsync(rate => rate.Year == year);
        if (ufesp == null)
        {
            return NotFound();
        }

        _dbContext.UfespRates.Remove(ufesp);
        await _dbContext.SaveChangesAsync();

        return NoContent();
    }

    public record UfespRequest(int Year, string Name, int Value, bool IsActive);
}
