using System.ComponentModel.DataAnnotations;

namespace Api.Dtos.Productivity;

public class FiscalActivityCreateDto
{
    [Required]
    public int ActivityId { get; set; }

    [Required]
    public int CompanyId { get; set; }

    [Required]
    public int FiscalUserId { get; set; }

    [Required]
    public DateTime CompletionDate { get; set; }

    [MaxLength(120)]
    public string? DocumentNumber { get; set; }

    [MaxLength(120)]
    public string? ProtocolNumber { get; set; }

    [MaxLength(80)]
    public string? Rc { get; set; }

    [MaxLength(40)]
    public string? CpfCnpj { get; set; }

    public decimal? Value { get; set; }
    public int? Quantity { get; set; }
    public string? Observation { get; set; }
}

public class FiscalActivityReadDto
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
}
