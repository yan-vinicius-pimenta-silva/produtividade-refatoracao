using Api.Data;
using Api.Dtos.Productivity;
using Api.Models;
using Microsoft.EntityFrameworkCore;

namespace Api.Services.ProductivityServices;

public class UfespRateService
{
    private readonly ApiDbContext _db;

    public UfespRateService(ApiDbContext db)
    {
        _db = db;
    }

    public async Task<List<UfespRateReadDto>> GetAllAsync()
    {
        return await _db.UfespRates
            .OrderByDescending(r => r.Year)
            .Select(r => new UfespRateReadDto
            {
                Id = r.Id,
                Year = r.Year,
                Value = r.Value,
                Active = r.Active
            })
            .ToListAsync();
    }

    public async Task<UfespRateReadDto?> GetByIdAsync(int id)
    {
        return await _db.UfespRates
            .Where(r => r.Id == id)
            .Select(r => new UfespRateReadDto
            {
                Id = r.Id,
                Year = r.Year,
                Value = r.Value,
                Active = r.Active
            })
            .FirstOrDefaultAsync();
    }

    public async Task<UfespRateReadDto> CreateAsync(UfespRateCreateDto dto)
    {
        var now = DateTime.UtcNow;
        var rate = new UfespRate
        {
            Year = dto.Year,
            Value = dto.Value,
            Active = dto.Active,
            CreatedAt = now,
            UpdatedAt = now
        };

        _db.UfespRates.Add(rate);
        await _db.SaveChangesAsync();

        return new UfespRateReadDto
        {
            Id = rate.Id,
            Year = rate.Year,
            Value = rate.Value,
            Active = rate.Active
        };
    }

    public async Task<UfespRateReadDto?> UpdateAsync(int id, UfespRateUpdateDto dto)
    {
        var rate = await _db.UfespRates.FirstOrDefaultAsync(r => r.Id == id);
        if (rate == null) return null;

        rate.Year = dto.Year;
        rate.Value = dto.Value;
        rate.Active = dto.Active;
        rate.UpdatedAt = DateTime.UtcNow;
        await _db.SaveChangesAsync();

        return new UfespRateReadDto
        {
            Id = rate.Id,
            Year = rate.Year,
            Value = rate.Value,
            Active = rate.Active
        };
    }

    public async Task<bool> DeleteAsync(int id)
    {
        var rate = await _db.UfespRates.FirstOrDefaultAsync(r => r.Id == id);
        if (rate == null) return false;
        _db.UfespRates.Remove(rate);
        await _db.SaveChangesAsync();
        return true;
    }

    public async Task<UfespRate?> GetActiveByYearAsync(int year)
    {
        return await _db.UfespRates.FirstOrDefaultAsync(r => r.Year == year && r.Active);
    }
}
