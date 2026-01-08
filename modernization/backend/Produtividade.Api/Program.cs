using System.Text;
using Microsoft.AspNetCore.Authentication.JwtBearer;
using Microsoft.EntityFrameworkCore;
using Microsoft.IdentityModel.Tokens;
using Produtividade.Api.Data;
using Produtividade.Api.Services;

var builder = WebApplication.CreateBuilder(args);

builder.Services.AddDbContext<ProdutividadeDbContext>(options =>
    options.UseSqlite(builder.Configuration.GetConnectionString("Produtividade")));

builder.Services.AddScoped<JwtService>();
builder.Services.AddScoped<PointsCalculator>();

builder.Services.AddControllers();

var secret = builder.Configuration["Jwt:Secret"] ?? string.Empty;
builder.Services.AddAuthentication(JwtBearerDefaults.AuthenticationScheme)
    .AddJwtBearer(options =>
    {
        options.TokenValidationParameters = new TokenValidationParameters
        {
            ValidateIssuer = true,
            ValidateAudience = true,
            ValidateIssuerSigningKey = true,
            ValidateLifetime = true,
            ValidIssuer = builder.Configuration["Jwt:Issuer"],
            ValidAudience = builder.Configuration["Jwt:Audience"],
            IssuerSigningKey = new SymmetricSecurityKey(Encoding.UTF8.GetBytes(secret))
        };
    });

var app = builder.Build();

app.UseAuthentication();
app.UseAuthorization();

app.MapControllers();

app.Run();
