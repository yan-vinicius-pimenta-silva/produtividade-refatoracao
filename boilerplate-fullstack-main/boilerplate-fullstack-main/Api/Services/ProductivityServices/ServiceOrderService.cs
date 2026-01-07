using Api.Data;
using Api.Dtos.Productivity;
using Api.Models;
using Microsoft.EntityFrameworkCore;

namespace Api.Services.ProductivityServices;

public class ServiceOrderService
{
    private readonly ApiDbContext _db;

    public ServiceOrderService(ApiDbContext db)
    {
        _db = db;
    }

    public async Task<List<ServiceOrderReadDto>> GetAllAsync(int? companyId = null)
    {
        var query = _db.ServiceOrders.AsQueryable();
        if (companyId.HasValue)
            query = query.Where(so => so.CompanyId == companyId.Value);

        return await query
            .OrderByDescending(so => so.CreatedAt)
            .Select(so => new ServiceOrderReadDto
            {
                Id = so.Id,
                CompanyId = so.CompanyId,
                ActivityId = so.ActivityId,
                FiscalUserId = so.FiscalUserId,
                ChiefUserId = so.ChiefUserId,
                Description = so.Description,
                Observation = so.Observation,
                Rc = so.Rc,
                DocumentNumber = so.DocumentNumber,
                ProtocolNumber = so.ProtocolNumber,
                IsResponded = so.IsResponded,
                Validated = so.Validated,
                Excluded = so.Excluded,
                DueDate = so.DueDate,
                CompletionDate = so.CompletionDate,
                CreatedAt = so.CreatedAt
            })
            .ToListAsync();
    }

    public async Task<ServiceOrderReadDto?> GetByIdAsync(int id)
    {
        return await _db.ServiceOrders
            .Where(so => so.Id == id)
            .Select(so => new ServiceOrderReadDto
            {
                Id = so.Id,
                CompanyId = so.CompanyId,
                ActivityId = so.ActivityId,
                FiscalUserId = so.FiscalUserId,
                ChiefUserId = so.ChiefUserId,
                Description = so.Description,
                Observation = so.Observation,
                Rc = so.Rc,
                DocumentNumber = so.DocumentNumber,
                ProtocolNumber = so.ProtocolNumber,
                IsResponded = so.IsResponded,
                Validated = so.Validated,
                Excluded = so.Excluded,
                DueDate = so.DueDate,
                CompletionDate = so.CompletionDate,
                CreatedAt = so.CreatedAt
            })
            .FirstOrDefaultAsync();
    }

    public async Task<ServiceOrderReadDto?> CreateAsync(ServiceOrderCreateDto dto)
    {
        var companyExists = await _db.Companies.AnyAsync(c => c.Id == dto.CompanyId);
        if (!companyExists) return null;

        var now = DateTime.UtcNow;
        var serviceOrder = new ServiceOrder
        {
            CompanyId = dto.CompanyId,
            ActivityId = dto.ActivityId,
            FiscalUserId = dto.FiscalUserId,
            ChiefUserId = dto.ChiefUserId,
            Description = dto.Description.Trim(),
            Observation = dto.Observation?.Trim(),
            Rc = dto.Rc?.Trim(),
            DocumentNumber = dto.DocumentNumber?.Trim(),
            ProtocolNumber = dto.ProtocolNumber?.Trim(),
            DueDate = dto.DueDate,
            CompletionDate = dto.CompletionDate,
            CreatedAt = now,
            UpdatedAt = now
        };

        _db.ServiceOrders.Add(serviceOrder);
        await _db.SaveChangesAsync();

        return new ServiceOrderReadDto
        {
            Id = serviceOrder.Id,
            CompanyId = serviceOrder.CompanyId,
            ActivityId = serviceOrder.ActivityId,
            FiscalUserId = serviceOrder.FiscalUserId,
            ChiefUserId = serviceOrder.ChiefUserId,
            Description = serviceOrder.Description,
            Observation = serviceOrder.Observation,
            Rc = serviceOrder.Rc,
            DocumentNumber = serviceOrder.DocumentNumber,
            ProtocolNumber = serviceOrder.ProtocolNumber,
            IsResponded = serviceOrder.IsResponded,
            Validated = serviceOrder.Validated,
            Excluded = serviceOrder.Excluded,
            DueDate = serviceOrder.DueDate,
            CompletionDate = serviceOrder.CompletionDate,
            CreatedAt = serviceOrder.CreatedAt
        };
    }

    public async Task<ServiceOrderReadDto?> UpdateAsync(int id, ServiceOrderUpdateDto dto)
    {
        var serviceOrder = await _db.ServiceOrders.FirstOrDefaultAsync(so => so.Id == id);
        if (serviceOrder == null) return null;

        serviceOrder.CompanyId = dto.CompanyId;
        serviceOrder.ActivityId = dto.ActivityId;
        serviceOrder.FiscalUserId = dto.FiscalUserId;
        serviceOrder.ChiefUserId = dto.ChiefUserId;
        serviceOrder.Description = dto.Description.Trim();
        serviceOrder.Observation = dto.Observation?.Trim();
        serviceOrder.Rc = dto.Rc?.Trim();
        serviceOrder.DocumentNumber = dto.DocumentNumber?.Trim();
        serviceOrder.ProtocolNumber = dto.ProtocolNumber?.Trim();
        serviceOrder.IsResponded = dto.IsResponded;
        serviceOrder.Validated = dto.Validated;
        serviceOrder.Excluded = dto.Excluded;
        serviceOrder.DueDate = dto.DueDate;
        serviceOrder.CompletionDate = dto.CompletionDate;
        serviceOrder.UpdatedAt = DateTime.UtcNow;

        await _db.SaveChangesAsync();

        return new ServiceOrderReadDto
        {
            Id = serviceOrder.Id,
            CompanyId = serviceOrder.CompanyId,
            ActivityId = serviceOrder.ActivityId,
            FiscalUserId = serviceOrder.FiscalUserId,
            ChiefUserId = serviceOrder.ChiefUserId,
            Description = serviceOrder.Description,
            Observation = serviceOrder.Observation,
            Rc = serviceOrder.Rc,
            DocumentNumber = serviceOrder.DocumentNumber,
            ProtocolNumber = serviceOrder.ProtocolNumber,
            IsResponded = serviceOrder.IsResponded,
            Validated = serviceOrder.Validated,
            Excluded = serviceOrder.Excluded,
            DueDate = serviceOrder.DueDate,
            CompletionDate = serviceOrder.CompletionDate,
            CreatedAt = serviceOrder.CreatedAt
        };
    }

    public async Task<bool> DeleteAsync(int id)
    {
        var serviceOrder = await _db.ServiceOrders.FirstOrDefaultAsync(so => so.Id == id);
        if (serviceOrder == null) return false;
        _db.ServiceOrders.Remove(serviceOrder);
        await _db.SaveChangesAsync();
        return true;
    }
}
