namespace Produtividade.Api.Models;

public class User
{
    public int Id { get; set; }
    public string Login { get; set; } = string.Empty;
    public string Name { get; set; } = string.Empty;
    public UserRole Role { get; set; }
    public int CompanyId { get; set; }
    public Company Company { get; set; } = null!;
}
