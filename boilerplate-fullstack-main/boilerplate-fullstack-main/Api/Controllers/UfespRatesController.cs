using Api.Dtos.Productivity;
using Api.Services.ProductivityServices;
using Microsoft.AspNetCore.Mvc;

namespace Api.Controllers;

[ApiController]
[Route("api/ufesp-rates")]
public class UfespRatesController : ControllerBase
{
    private readonly UfespRateService _service;

    public UfespRatesController(UfespRateService service)
    {
        _service = service;
    }

    [HttpGet]
    public async Task<IActionResult> GetAll()
    {
        var rates = await _service.GetAllAsync();
        return Ok(rates);
    }

    [HttpGet("{id:int}")]
    public async Task<IActionResult> GetById(int id)
    {
        var rate = await _service.GetByIdAsync(id);
        if (rate == null) return NotFound(new { message = "UFESP não encontrada." });
        return Ok(rate);
    }

    [HttpPost]
    public async Task<IActionResult> Create([FromBody] UfespRateCreateDto dto)
    {
        if (dto == null) return BadRequest(new { message = "Payload inválido." });
        var created = await _service.CreateAsync(dto);
        return CreatedAtAction(nameof(GetById), new { id = created.Id }, created);
    }

    [HttpPut("{id:int}")]
    public async Task<IActionResult> Update(int id, [FromBody] UfespRateUpdateDto dto)
    {
        if (dto == null) return BadRequest(new { message = "Payload inválido." });
        var updated = await _service.UpdateAsync(id, dto);
        if (updated == null) return NotFound(new { message = "UFESP não encontrada." });
        return Ok(updated);
    }

    [HttpDelete("{id:int}")]
    public async Task<IActionResult> Delete(int id)
    {
        var deleted = await _service.DeleteAsync(id);
        if (!deleted) return NotFound(new { message = "UFESP não encontrada." });
        return NoContent();
    }
}
