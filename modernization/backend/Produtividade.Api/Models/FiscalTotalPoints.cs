namespace Produtividade.Api.Models;

public class FiscalTotalPoints
{
    public int FiscalId { get; set; }
    public User Fiscal { get; set; } = null!;
    public DateTime EffectiveDate { get; set; }
    public int TotalPoints { get; set; }
    public int PointsDeduction { get; set; }
    public int PointsUfesp { get; set; }
    public int PointsPontuacao { get; set; }
    public int TotalCollected { get; set; }
    public int RemainingBalance { get; set; }
    public int RemainingUsed { get; set; }
    public DateTime CreatedAt { get; set; } = DateTime.UtcNow;
}
