using System.IdentityModel.Tokens.Jwt;
using System.Text;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using Microsoft.IdentityModel.Tokens;
using Produtividade.Api.Data;
using Produtividade.Api.Services;

namespace Produtividade.Api.Controllers;

[ApiController]
[Route("api/auth")]
public class AuthController : ControllerBase
{
    private readonly ProdutividadeDbContext _dbContext;
    private readonly IConfiguration _configuration;
    private readonly JwtService _jwtService;

    public AuthController(ProdutividadeDbContext dbContext, IConfiguration configuration, JwtService jwtService)
    {
        _dbContext = dbContext;
        _configuration = configuration;
        _jwtService = jwtService;
    }

    [HttpPost("login")]
    public async Task<ActionResult<AuthResponse>> Login([FromBody] LoginRequest request)
    {
        var login = request.Login;
        if (!string.IsNullOrWhiteSpace(request.Token))
        {
            login = GetLoginFromToken(request.Token);
        }

        if (string.IsNullOrWhiteSpace(login) && _configuration.GetValue<bool>("Login:AllowDevLogin"))
        {
            login = _configuration["Login:DevLogin"];
        }

        if (string.IsNullOrWhiteSpace(login))
        {
            return Unauthorized();
        }

        var user = await _dbContext.Users
            .Include(u => u.Company)
            .FirstOrDefaultAsync(u => u.Login == login);

        if (user == null)
        {
            return Unauthorized();
        }

        return Ok(new AuthResponse
        {
            Token = _jwtService.CreateToken(user),
            User = new UserSummary(user.Id, user.Login, user.Name, (int)user.Role, user.CompanyId, user.Company.Name)
        });
    }

    private string? GetLoginFromToken(string token)
    {
        var secret = _configuration["Jwt:Secret"] ?? string.Empty;
        var handler = new JwtSecurityTokenHandler();
        var parameters = new TokenValidationParameters
        {
            ValidateIssuer = true,
            ValidateAudience = true,
            ValidateIssuerSigningKey = true,
            ValidateLifetime = true,
            ValidIssuer = _configuration["Jwt:Issuer"],
            ValidAudience = _configuration["Jwt:Audience"],
            IssuerSigningKey = new SymmetricSecurityKey(Encoding.UTF8.GetBytes(secret))
        };

        var principal = handler.ValidateToken(token, parameters, out _);
        return principal.Identity?.Name ?? principal.FindFirst(JwtRegisteredClaimNames.Sub)?.Value;
    }

    public record LoginRequest(string? Login, string? Token);
    public record UserSummary(int Id, string Login, string Name, int Role, int CompanyId, string CompanyName);

    public class AuthResponse
    {
        public string Token { get; set; } = string.Empty;
        public UserSummary User { get; set; } = null!;
    }
}
