using Api.Dtos.Productivity;
using Api.Services.ProductivityServices;
using Microsoft.AspNetCore.Mvc;

namespace Api.Controllers;

[ApiController]
[Route("api/fiscal-activities")]
public class FiscalActivitiesController : ControllerBase
{
    private readonly FiscalActivityService _service;

    public FiscalActivitiesController(FiscalActivityService service)
    {
        _service = service;
    }

    [HttpGet]
    public async Task<IActionResult> GetAll([FromQuery] int? companyId = null, [FromQuery] int? fiscalUserId = null)
    {
        var activities = await _service.GetAllAsync(companyId, fiscalUserId);
        return Ok(activities);
    }

    [HttpPost]
    public async Task<IActionResult> Create([FromBody] FiscalActivityCreateDto dto)
    {
        if (dto == null) return BadRequest(new { message = "Payload inválido." });

        try
        {
            var (created, error) = await _service.CreateAsync(dto);
            if (created == null)
                return BadRequest(new { message = error ?? "Falha ao cadastrar atividade." });

            return CreatedAtAction(nameof(GetAll), new { id = created.Id }, created);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
    }
}
