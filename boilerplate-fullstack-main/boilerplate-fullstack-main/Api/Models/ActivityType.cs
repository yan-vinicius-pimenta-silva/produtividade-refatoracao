namespace Api.Models;

public class ActivityType
{
    public int Id { get; set; }
    public string Name { get; set; } = string.Empty;
    public bool Active { get; set; } = true;
    public DateTime CreatedAt { get; set; }
    public DateTime UpdatedAt { get; set; }

    public ICollection<Activity> Activities { get; set; } = new List<Activity>();
}
