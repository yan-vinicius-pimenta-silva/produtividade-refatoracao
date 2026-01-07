using Api.Dtos.Productivity;
using Api.Services.ProductivityServices;
using Microsoft.AspNetCore.Mvc;

namespace Api.Controllers;

[ApiController]
[Route("api/activity-types")]
public class ActivityTypesController : ControllerBase
{
    private readonly ActivityTypeService _service;

    public ActivityTypesController(ActivityTypeService service)
    {
        _service = service;
    }

    [HttpGet]
    public async Task<IActionResult> GetAll()
    {
        var types = await _service.GetAllAsync();
        return Ok(types);
    }

    [HttpGet("{id:int}")]
    public async Task<IActionResult> GetById(int id)
    {
        var type = await _service.GetByIdAsync(id);
        if (type == null) return NotFound(new { message = "Tipo de atividade não encontrado." });
        return Ok(type);
    }

    [HttpPost]
    public async Task<IActionResult> Create([FromBody] ActivityTypeCreateDto dto)
    {
        if (dto == null) return BadRequest(new { message = "Payload inválido." });
        var created = await _service.CreateAsync(dto);
        return CreatedAtAction(nameof(GetById), new { id = created.Id }, created);
    }

    [HttpPut("{id:int}")]
    public async Task<IActionResult> Update(int id, [FromBody] ActivityTypeUpdateDto dto)
    {
        if (dto == null) return BadRequest(new { message = "Payload inválido." });
        var updated = await _service.UpdateAsync(id, dto);
        if (updated == null) return NotFound(new { message = "Tipo de atividade não encontrado." });
        return Ok(updated);
    }

    [HttpDelete("{id:int}")]
    public async Task<IActionResult> Delete(int id)
    {
        var deleted = await _service.DeleteAsync(id);
        if (!deleted) return NotFound(new { message = "Tipo de atividade não encontrado." });
        return NoContent();
    }
}
