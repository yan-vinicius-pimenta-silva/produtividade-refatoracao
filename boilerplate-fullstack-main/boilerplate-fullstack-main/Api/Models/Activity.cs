namespace Api.Models;

public class Activity
{
    public int Id { get; set; }
    public int CompanyId { get; set; }
    public int ActivityTypeId { get; set; }
    public string Name { get; set; } = string.Empty;
    public int PointsBase { get; set; }
    public bool Active { get; set; } = true;
    public bool Deleted { get; set; } = false;
    public DateTime CreatedAt { get; set; }
    public DateTime UpdatedAt { get; set; }

    public Company? Company { get; set; }
    public ActivityType? ActivityType { get; set; }
    public ICollection<FiscalActivity> FiscalActivities { get; set; } = new List<FiscalActivity>();
    public ICollection<ServiceOrder> ServiceOrders { get; set; } = new List<ServiceOrder>();
}
