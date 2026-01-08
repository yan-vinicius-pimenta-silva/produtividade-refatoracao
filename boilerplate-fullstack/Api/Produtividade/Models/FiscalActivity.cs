namespace Api.Produtividade.Models;

public class FiscalActivity
{
    public long Id { get; set; }
    public int ActivityId { get; set; }
    public Activity Activity { get; set; } = null!;
    public int FiscalId { get; set; }
    public User Fiscal { get; set; } = null!;
    public int CompanyId { get; set; }
    public Company Company { get; set; } = null!;
    public string? UfespYear { get; set; }
    public string? Document { get; set; }
    public string? Protocol { get; set; }
    public string? CpfCnpj { get; set; }
    public string? Rc { get; set; }
    public int? Value { get; set; }
    public int? Quantity { get; set; }
    public int? TotalPoints { get; set; }
    public DateTime? ValidatedAt { get; set; }
    public string? ValidatedBy { get; set; }
    public string? DeletedBy { get; set; }
    public string? DeleteReason { get; set; }
    public string? Notes { get; set; }
    public DateTime CreatedAt { get; set; } = DateTime.UtcNow;
    public DateTime? UpdatedAt { get; set; }
    public DateTime? DeletedAt { get; set; }
    public DateTime? CompletedAt { get; set; }
    public ICollection<FiscalActivityAttachment> Attachments { get; set; } = new List<FiscalActivityAttachment>();
}
