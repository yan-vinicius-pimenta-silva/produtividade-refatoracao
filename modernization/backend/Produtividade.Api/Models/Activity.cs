namespace Produtividade.Api.Models;

public class Activity
{
    public int Id { get; set; }
    public string Description { get; set; } = string.Empty;
    public int Points { get; set; }
    public bool IsActive { get; set; } = true;
    public bool IsOsActivity { get; set; }
    public bool HasMultiplicator { get; set; }
    public int ActivityTypeId { get; set; }
    public ActivityType ActivityType { get; set; } = null!;
    public int CompanyId { get; set; }
    public Company Company { get; set; } = null!;
}
