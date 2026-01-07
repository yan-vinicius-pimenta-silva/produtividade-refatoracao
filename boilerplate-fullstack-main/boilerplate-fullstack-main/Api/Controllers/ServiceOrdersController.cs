using Api.Dtos.Productivity;
using Api.Services.ProductivityServices;
using Microsoft.AspNetCore.Mvc;

namespace Api.Controllers;

[ApiController]
[Route("api/service-orders")]
public class ServiceOrdersController : ControllerBase
{
    private readonly ServiceOrderService _service;

    public ServiceOrdersController(ServiceOrderService service)
    {
        _service = service;
    }

    [HttpGet]
    public async Task<IActionResult> GetAll([FromQuery] int? companyId = null)
    {
        var orders = await _service.GetAllAsync(companyId);
        return Ok(orders);
    }

    [HttpGet("{id:int}")]
    public async Task<IActionResult> GetById(int id)
    {
        var order = await _service.GetByIdAsync(id);
        if (order == null) return NotFound(new { message = "Ordem de serviço não encontrada." });
        return Ok(order);
    }

    [HttpPost]
    public async Task<IActionResult> Create([FromBody] ServiceOrderCreateDto dto)
    {
        if (dto == null) return BadRequest(new { message = "Payload inválido." });
        var created = await _service.CreateAsync(dto);
        if (created == null) return BadRequest(new { message = "Empresa inválida." });
        return CreatedAtAction(nameof(GetById), new { id = created.Id }, created);
    }

    [HttpPut("{id:int}")]
    public async Task<IActionResult> Update(int id, [FromBody] ServiceOrderUpdateDto dto)
    {
        if (dto == null) return BadRequest(new { message = "Payload inválido." });
        var updated = await _service.UpdateAsync(id, dto);
        if (updated == null) return NotFound(new { message = "Ordem de serviço não encontrada." });
        return Ok(updated);
    }

    [HttpDelete("{id:int}")]
    public async Task<IActionResult> Delete(int id)
    {
        var deleted = await _service.DeleteAsync(id);
        if (!deleted) return NotFound(new { message = "Ordem de serviço não encontrada." });
        return NoContent();
    }
}
