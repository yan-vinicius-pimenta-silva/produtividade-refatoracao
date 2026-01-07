namespace Api.Models;

public class ServiceOrderHistory
{
    public int Id { get; set; }
    public int ServiceOrderId { get; set; }
    public int UserId { get; set; }
    public string Status { get; set; } = "OPEN";
    public string? StatusColor { get; set; }
    public string? Observation { get; set; }
    public string? AttachmentPath { get; set; }
    public DateTime CreatedAt { get; set; }
    public DateTime UpdatedAt { get; set; }

    public ServiceOrder? ServiceOrder { get; set; }
    public User? User { get; set; }
}
