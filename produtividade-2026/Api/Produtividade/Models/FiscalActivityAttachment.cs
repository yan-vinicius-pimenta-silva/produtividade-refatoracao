namespace Api.Produtividade.Models;

public class FiscalActivityAttachment
{
    public long Id { get; set; }
    public long FiscalActivityId { get; set; }
    public FiscalActivity FiscalActivity { get; set; } = null!;
    public string Path { get; set; } = string.Empty;
    public DateTime CreatedAt { get; set; } = DateTime.UtcNow;
}
