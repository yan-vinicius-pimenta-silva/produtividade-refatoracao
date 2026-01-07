using Api.Data;
using Api.Dtos.Productivity;
using Api.Models;
using Microsoft.EntityFrameworkCore;

namespace Api.Services.ProductivityServices;

public class ActivityService
{
    private readonly ApiDbContext _db;

    public ActivityService(ApiDbContext db)
    {
        _db = db;
    }

    public async Task<List<ActivityReadDto>> GetAllAsync(int? companyId = null)
    {
        var query = _db.Activities.AsQueryable();
        if (companyId.HasValue)
            query = query.Where(a => a.CompanyId == companyId.Value);

        return await query
            .OrderBy(a => a.Name)
            .Select(a => new ActivityReadDto
            {
                Id = a.Id,
                CompanyId = a.CompanyId,
                ActivityTypeId = a.ActivityTypeId,
                Name = a.Name,
                PointsBase = a.PointsBase,
                Active = a.Active,
                Deleted = a.Deleted
            })
            .ToListAsync();
    }

    public async Task<ActivityReadDto?> GetByIdAsync(int id)
    {
        return await _db.Activities
            .Where(a => a.Id == id)
            .Select(a => new ActivityReadDto
            {
                Id = a.Id,
                CompanyId = a.CompanyId,
                ActivityTypeId = a.ActivityTypeId,
                Name = a.Name,
                PointsBase = a.PointsBase,
                Active = a.Active,
                Deleted = a.Deleted
            })
            .FirstOrDefaultAsync();
    }

    public async Task<ActivityReadDto?> CreateAsync(ActivityCreateDto dto)
    {
        var companyExists = await _db.Companies.AnyAsync(c => c.Id == dto.CompanyId);
        var typeExists = await _db.ActivityTypes.AnyAsync(t => t.Id == dto.ActivityTypeId);
        if (!companyExists || !typeExists) return null;

        var now = DateTime.UtcNow;
        var activity = new Activity
        {
            CompanyId = dto.CompanyId,
            ActivityTypeId = dto.ActivityTypeId,
            Name = dto.Name.Trim(),
            PointsBase = dto.PointsBase,
            CreatedAt = now,
            UpdatedAt = now
        };

        _db.Activities.Add(activity);
        await _db.SaveChangesAsync();

        return new ActivityReadDto
        {
            Id = activity.Id,
            CompanyId = activity.CompanyId,
            ActivityTypeId = activity.ActivityTypeId,
            Name = activity.Name,
            PointsBase = activity.PointsBase,
            Active = activity.Active,
            Deleted = activity.Deleted
        };
    }

    public async Task<ActivityReadDto?> UpdateAsync(int id, ActivityUpdateDto dto)
    {
        var activity = await _db.Activities.FirstOrDefaultAsync(a => a.Id == id);
        if (activity == null) return null;

        activity.CompanyId = dto.CompanyId;
        activity.ActivityTypeId = dto.ActivityTypeId;
        activity.Name = dto.Name.Trim();
        activity.PointsBase = dto.PointsBase;
        activity.Active = dto.Active;
        activity.Deleted = dto.Deleted;
        activity.UpdatedAt = DateTime.UtcNow;

        await _db.SaveChangesAsync();

        return new ActivityReadDto
        {
            Id = activity.Id,
            CompanyId = activity.CompanyId,
            ActivityTypeId = activity.ActivityTypeId,
            Name = activity.Name,
            PointsBase = activity.PointsBase,
            Active = activity.Active,
            Deleted = activity.Deleted
        };
    }

    public async Task<bool> DeleteAsync(int id)
    {
        var activity = await _db.Activities.FirstOrDefaultAsync(a => a.Id == id);
        if (activity == null) return false;
        _db.Activities.Remove(activity);
        await _db.SaveChangesAsync();
        return true;
    }
}
