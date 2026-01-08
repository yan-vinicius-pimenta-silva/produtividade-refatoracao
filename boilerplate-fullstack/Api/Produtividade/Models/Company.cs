namespace Api.Produtividade.Models;

public class Company
{
    public int Id { get; set; }
    public string Name { get; set; } = string.Empty;
    public string? Email { get; set; }
    public string? Secretaria { get; set; }
    public string? Divisao { get; set; }
    public string? Telefone { get; set; }
}
