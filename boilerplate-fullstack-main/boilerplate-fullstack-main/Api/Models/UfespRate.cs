namespace Api.Models;

public class UfespRate
{
    public int Id { get; set; }
    public int Year { get; set; }
    public int Value { get; set; }
    public bool Active { get; set; } = true;
    public DateTime CreatedAt { get; set; }
    public DateTime UpdatedAt { get; set; }
}
