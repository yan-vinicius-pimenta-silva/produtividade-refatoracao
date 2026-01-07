using Api.Dtos.Productivity;
using Api.Services.ProductivityServices;
using Microsoft.AspNetCore.Mvc;

namespace Api.Controllers;

[ApiController]
[Route("api/activities")]
public class ActivitiesController : ControllerBase
{
    private readonly ActivityService _service;

    public ActivitiesController(ActivityService service)
    {
        _service = service;
    }

    [HttpGet]
    public async Task<IActionResult> GetAll([FromQuery] int? companyId = null)
    {
        var activities = await _service.GetAllAsync(companyId);
        return Ok(activities);
    }

    [HttpGet("{id:int}")]
    public async Task<IActionResult> GetById(int id)
    {
        var activity = await _service.GetByIdAsync(id);
        if (activity == null) return NotFound(new { message = "Atividade não encontrada." });
        return Ok(activity);
    }

    [HttpPost]
    public async Task<IActionResult> Create([FromBody] ActivityCreateDto dto)
    {
        if (dto == null) return BadRequest(new { message = "Payload inválido." });
        var created = await _service.CreateAsync(dto);
        if (created == null) return BadRequest(new { message = "Empresa ou tipo inválidos." });
        return CreatedAtAction(nameof(GetById), new { id = created.Id }, created);
    }

    [HttpPut("{id:int}")]
    public async Task<IActionResult> Update(int id, [FromBody] ActivityUpdateDto dto)
    {
        if (dto == null) return BadRequest(new { message = "Payload inválido." });
        var updated = await _service.UpdateAsync(id, dto);
        if (updated == null) return NotFound(new { message = "Atividade não encontrada." });
        return Ok(updated);
    }

    [HttpDelete("{id:int}")]
    public async Task<IActionResult> Delete(int id)
    {
        var deleted = await _service.DeleteAsync(id);
        if (!deleted) return NotFound(new { message = "Atividade não encontrada." });
        return NoContent();
    }
}
