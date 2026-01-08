using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using Api.Produtividade.Data;
using Api.Produtividade.Models;

namespace Api.Produtividade.Controllers;

[ApiController]
[Route("api/produtividade/users")]
public class UsersController : ControllerBase
{
    private readonly ProdutividadeDbContext _dbContext;

    public UsersController(ProdutividadeDbContext dbContext)
    {
        _dbContext = dbContext;
    }

    [HttpGet]
    public async Task<ActionResult<List<UserSummary>>> List([FromQuery] UserRole? role)
    {
        var query = _dbContext.Users
            .AsNoTracking()
            .Include(user => user.Company)
            .AsQueryable();

        if (role.HasValue)
        {
            query = query.Where(user => user.Role == role.Value);
        }

        var users = await query
            .OrderBy(user => user.Name)
            .Select(user => new UserSummary
            {
                Id = user.Id,
                Name = user.Name,
                Login = user.Login,
                Role = user.Role,
                CompanyId = user.CompanyId,
                CompanyName = user.Company.Name
            })
            .ToListAsync();

        return Ok(users);
    }

    public record UserSummary
    {
        public int Id { get; init; }
        public string Name { get; init; } = string.Empty;
        public string Login { get; init; } = string.Empty;
        public UserRole Role { get; init; }
        public int CompanyId { get; init; }
        public string CompanyName { get; init; } = string.Empty;
    }
}
