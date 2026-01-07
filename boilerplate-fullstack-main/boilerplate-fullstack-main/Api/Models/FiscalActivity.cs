namespace Api.Models;

public class FiscalActivity
{
    public int Id { get; set; }
    public int ActivityId { get; set; }
    public int CompanyId { get; set; }
    public int FiscalUserId { get; set; }
    public string? DocumentNumber { get; set; }
    public string? ProtocolNumber { get; set; }
    public string? Rc { get; set; }
    public string? CpfCnpj { get; set; }
    public int? UfespYear { get; set; }
    public int? UfespValue { get; set; }
    public int? Quantity { get; set; }
    public int? PointsTotal { get; set; }
    public decimal? Value { get; set; }
    public string? Observation { get; set; }
    public bool Validated { get; set; }
    public DateTime CompletionDate { get; set; }
    public DateTime CreatedAt { get; set; }
    public DateTime UpdatedAt { get; set; }
    public DateTime? ValidatedAt { get; set; }

    public Activity? Activity { get; set; }
    public Company? Company { get; set; }
    public User? FiscalUser { get; set; }
}
