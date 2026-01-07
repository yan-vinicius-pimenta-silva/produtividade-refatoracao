namespace Api.Models;

public class Company
{
    public int Id { get; set; }
    public string Name { get; set; } = string.Empty;
    public string? Email { get; set; }
    public string? Secretary { get; set; }
    public string? Division { get; set; }
    public string? Phone { get; set; }
    public string? LogoUrl { get; set; }
    public string? ParametersJson { get; set; }
    public bool Active { get; set; } = true;
    public bool Deleted { get; set; } = false;
    public DateTime CreatedAt { get; set; }
    public DateTime UpdatedAt { get; set; }

    public ICollection<Activity> Activities { get; set; } = new List<Activity>();
    public ICollection<FiscalActivity> FiscalActivities { get; set; } = new List<FiscalActivity>();
    public ICollection<ServiceOrder> ServiceOrders { get; set; } = new List<ServiceOrder>();
}
