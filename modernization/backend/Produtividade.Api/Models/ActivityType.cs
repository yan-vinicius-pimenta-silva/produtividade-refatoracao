namespace Produtividade.Api.Models;

public class ActivityType
{
    public int Id { get; set; }
    public string Name { get; set; } = string.Empty;
    public ActivityCalculationType CalculationType { get; set; }
    public bool IsActive { get; set; } = true;
}
