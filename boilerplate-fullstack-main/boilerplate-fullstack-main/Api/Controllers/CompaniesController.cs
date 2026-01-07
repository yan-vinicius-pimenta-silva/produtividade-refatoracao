using Api.Dtos.Productivity;
using Api.Services.ProductivityServices;
using Microsoft.AspNetCore.Mvc;

namespace Api.Controllers;

[ApiController]
[Route("api/companies")]
public class CompaniesController : ControllerBase
{
    private readonly CompanyService _service;

    public CompaniesController(CompanyService service)
    {
        _service = service;
    }

    [HttpGet]
    public async Task<IActionResult> GetAll()
    {
        var companies = await _service.GetAllAsync();
        return Ok(companies);
    }

    [HttpGet("{id:int}")]
    public async Task<IActionResult> GetById(int id)
    {
        var company = await _service.GetByIdAsync(id);
        if (company == null) return NotFound(new { message = "Empresa não encontrada." });
        return Ok(company);
    }

    [HttpPost]
    public async Task<IActionResult> Create([FromBody] CompanyCreateDto dto)
    {
        if (dto == null) return BadRequest(new { message = "Payload inválido." });
        var created = await _service.CreateAsync(dto);
        return CreatedAtAction(nameof(GetById), new { id = created.Id }, created);
    }

    [HttpPut("{id:int}")]
    public async Task<IActionResult> Update(int id, [FromBody] CompanyUpdateDto dto)
    {
        if (dto == null) return BadRequest(new { message = "Payload inválido." });
        var updated = await _service.UpdateAsync(id, dto);
        if (updated == null) return NotFound(new { message = "Empresa não encontrada." });
        return Ok(updated);
    }

    [HttpDelete("{id:int}")]
    public async Task<IActionResult> Delete(int id)
    {
        var deleted = await _service.DeleteAsync(id);
        if (!deleted) return NotFound(new { message = "Empresa não encontrada." });
        return NoContent();
    }
}
