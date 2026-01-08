using System.Security.Claims;
using Api.Helpers;
using Api.Produtividade.Models;

namespace Api.Produtividade.Services;

public class JwtService
{
    public string CreateToken(User user)
    {
        var claims = new[]
        {
            new Claim("login", user.Login),
            new Claim("role", ((int)user.Role).ToString()),
            new Claim("companyId", user.CompanyId.ToString()),
            new Claim("name", user.Name)
        };

        return JsonWebToken.Create(claims, expireMinutes: 480);
    }
}
