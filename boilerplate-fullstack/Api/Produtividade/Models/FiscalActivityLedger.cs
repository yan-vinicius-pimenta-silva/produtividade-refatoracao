namespace Api.Produtividade.Models;

public class FiscalActivityLedger
{
    public int ActivityId { get; set; }
    public Activity Activity { get; set; } = null!;
    public int FiscalId { get; set; }
    public User Fiscal { get; set; } = null!;
    public DateTime EffectiveDate { get; set; }
    public int? UfespValue { get; set; }
    public int? TotalValue { get; set; }
    public int BasePoint { get; set; }
    public int TotalPoints { get; set; }
    public int? Quantity { get; set; }
    public DateTime CreatedAt { get; set; } = DateTime.UtcNow;
}
