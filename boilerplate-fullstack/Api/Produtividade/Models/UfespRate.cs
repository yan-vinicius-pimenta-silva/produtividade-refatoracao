namespace Api.Produtividade.Models;

public class UfespRate
{
    public int Year { get; set; }
    public string Name { get; set; } = string.Empty;
    public int Value { get; set; }
    public bool IsActive { get; set; }
}
