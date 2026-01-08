using System.IdentityModel.Tokens.Jwt;
using System.Security.Claims;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using Api.Helpers;
using Api.Produtividade.Data;
using Api.Produtividade.Services;

namespace Api.Produtividade.Controllers;

[ApiController]
[Route("api/produtividade/auth")]
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
        try
        {
            var principal = JsonWebToken.Decode(token);
            return FindClaimValue(principal, "login")
                ?? FindClaimValue(principal, "username")
                ?? principal.FindFirst(JwtRegisteredClaimNames.Sub)?.Value
                ?? principal.Identity?.Name;
        }
        catch
        {
            return null;
        }
    }

    private static string? FindClaimValue(ClaimsPrincipal principal, string type)
    {
        return principal.Claims.FirstOrDefault(claim => claim.Type == type)?.Value;
    }

    public record LoginRequest(string? Login, string? Token);
    public record UserSummary(int Id, string Login, string Name, int Role, int CompanyId, string CompanyName);

    public class AuthResponse
    {
        public string Token { get; set; } = string.Empty;
        public UserSummary User { get; set; } = null!;
    }
}
