namespace Api.Models;

public class ServiceOrder
{
    public int Id { get; set; }
    public int CompanyId { get; set; }
    public int? ActivityId { get; set; }
    public int? FiscalUserId { get; set; }
    public int? ChiefUserId { get; set; }
    public string Description { get; set; } = string.Empty;
    public string? Observation { get; set; }
    public string? Rc { get; set; }
    public string? DocumentNumber { get; set; }
    public string? ProtocolNumber { get; set; }
    public bool IsResponded { get; set; }
    public bool Validated { get; set; }
    public bool Excluded { get; set; }
    public DateTime? DueDate { get; set; }
    public DateTime? CompletionDate { get; set; }
    public DateTime CreatedAt { get; set; }
    public DateTime UpdatedAt { get; set; }

    public Company? Company { get; set; }
    public Activity? Activity { get; set; }
    public User? FiscalUser { get; set; }
    public User? ChiefUser { get; set; }
    public ICollection<ServiceOrderHistory> History { get; set; } = new List<ServiceOrderHistory>();
}
