using Api.Data;
using Api.Dtos.Productivity;
using Api.Models;
using Microsoft.EntityFrameworkCore;

namespace Api.Services.ProductivityServices;

public class CompanyService
{
    private readonly ApiDbContext _db;

    public CompanyService(ApiDbContext db)
    {
        _db = db;
    }

    public async Task<List<CompanyReadDto>> GetAllAsync()
    {
        return await _db.Companies
            .OrderBy(c => c.Name)
            .Select(c => new CompanyReadDto
            {
                Id = c.Id,
                Name = c.Name,
                Email = c.Email,
                Secretary = c.Secretary,
                Division = c.Division,
                Phone = c.Phone,
                LogoUrl = c.LogoUrl,
                ParametersJson = c.ParametersJson,
                Active = c.Active,
                Deleted = c.Deleted
            })
            .ToListAsync();
    }

    public async Task<CompanyReadDto?> GetByIdAsync(int id)
    {
        return await _db.Companies
            .Where(c => c.Id == id)
            .Select(c => new CompanyReadDto
            {
                Id = c.Id,
                Name = c.Name,
                Email = c.Email,
                Secretary = c.Secretary,
                Division = c.Division,
                Phone = c.Phone,
                LogoUrl = c.LogoUrl,
                ParametersJson = c.ParametersJson,
                Active = c.Active,
                Deleted = c.Deleted
            })
            .FirstOrDefaultAsync();
    }

    public async Task<CompanyReadDto> CreateAsync(CompanyCreateDto dto)
    {
        var now = DateTime.UtcNow;
        var company = new Company
        {
            Name = dto.Name.Trim(),
            Email = dto.Email?.Trim(),
            Secretary = dto.Secretary?.Trim(),
            Division = dto.Division?.Trim(),
            Phone = dto.Phone?.Trim(),
            LogoUrl = dto.LogoUrl?.Trim(),
            ParametersJson = dto.ParametersJson,
            CreatedAt = now,
            UpdatedAt = now
        };

        _db.Companies.Add(company);
        await _db.SaveChangesAsync();

        return new CompanyReadDto
        {
            Id = company.Id,
            Name = company.Name,
            Email = company.Email,
            Secretary = company.Secretary,
            Division = company.Division,
            Phone = company.Phone,
            LogoUrl = company.LogoUrl,
            ParametersJson = company.ParametersJson,
            Active = company.Active,
            Deleted = company.Deleted
        };
    }

    public async Task<CompanyReadDto?> UpdateAsync(int id, CompanyUpdateDto dto)
    {
        var company = await _db.Companies.FirstOrDefaultAsync(c => c.Id == id);
        if (company == null) return null;

        company.Name = dto.Name.Trim();
        company.Email = dto.Email?.Trim();
        company.Secretary = dto.Secretary?.Trim();
        company.Division = dto.Division?.Trim();
        company.Phone = dto.Phone?.Trim();
        company.LogoUrl = dto.LogoUrl?.Trim();
        company.ParametersJson = dto.ParametersJson;
        company.Active = dto.Active;
        company.Deleted = dto.Deleted;
        company.UpdatedAt = DateTime.UtcNow;

        await _db.SaveChangesAsync();

        return new CompanyReadDto
        {
            Id = company.Id,
            Name = company.Name,
            Email = company.Email,
            Secretary = company.Secretary,
            Division = company.Division,
            Phone = company.Phone,
            LogoUrl = company.LogoUrl,
            ParametersJson = company.ParametersJson,
            Active = company.Active,
            Deleted = company.Deleted
        };
    }

    public async Task<bool> DeleteAsync(int id)
    {
        var company = await _db.Companies.FirstOrDefaultAsync(c => c.Id == id);
        if (company == null) return false;
        _db.Companies.Remove(company);
        await _db.SaveChangesAsync();
        return true;
    }
}
