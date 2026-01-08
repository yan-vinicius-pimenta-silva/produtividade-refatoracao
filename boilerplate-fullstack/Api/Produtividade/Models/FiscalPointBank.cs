namespace Api.Produtividade.Models;

public class FiscalPointBank
{
    public int FiscalId { get; set; }
    public User Fiscal { get; set; } = null!;
    public DateTime EffectiveDate { get; set; }
    public int RemainingBalance { get; set; }
    public DateTime CreatedAt { get; set; } = DateTime.UtcNow;
}
