using Api.Data;
using Api.Dtos.Productivity;
using Api.Models;
using Microsoft.EntityFrameworkCore;

namespace Api.Services.ProductivityServices;

public class ActivityTypeService
{
    private readonly ApiDbContext _db;

    public ActivityTypeService(ApiDbContext db)
    {
        _db = db;
    }

    public async Task<List<ActivityTypeReadDto>> GetAllAsync()
    {
        return await _db.ActivityTypes
            .OrderBy(t => t.Name)
            .Select(t => new ActivityTypeReadDto
            {
                Id = t.Id,
                Name = t.Name,
                Active = t.Active
            })
            .ToListAsync();
    }

    public async Task<ActivityTypeReadDto?> GetByIdAsync(int id)
    {
        return await _db.ActivityTypes
            .Where(t => t.Id == id)
            .Select(t => new ActivityTypeReadDto
            {
                Id = t.Id,
                Name = t.Name,
                Active = t.Active
            })
            .FirstOrDefaultAsync();
    }

    public async Task<ActivityTypeReadDto> CreateAsync(ActivityTypeCreateDto dto)
    {
        var now = DateTime.UtcNow;
        var type = new ActivityType
        {
            Name = dto.Name.Trim().ToUpperInvariant(),
            CreatedAt = now,
            UpdatedAt = now
        };

        _db.ActivityTypes.Add(type);
        await _db.SaveChangesAsync();

        return new ActivityTypeReadDto
        {
            Id = type.Id,
            Name = type.Name,
            Active = type.Active
        };
    }

    public async Task<ActivityTypeReadDto?> UpdateAsync(int id, ActivityTypeUpdateDto dto)
    {
        var type = await _db.ActivityTypes.FirstOrDefaultAsync(t => t.Id == id);
        if (type == null) return null;

        type.Name = dto.Name.Trim().ToUpperInvariant();
        type.Active = dto.Active;
        type.UpdatedAt = DateTime.UtcNow;
        await _db.SaveChangesAsync();

        return new ActivityTypeReadDto
        {
            Id = type.Id,
            Name = type.Name,
            Active = type.Active
        };
    }

    public async Task<bool> DeleteAsync(int id)
    {
        var type = await _db.ActivityTypes.FirstOrDefaultAsync(t => t.Id == id);
        if (type == null) return false;
        _db.ActivityTypes.Remove(type);
        await _db.SaveChangesAsync();
        return true;
    }
}
