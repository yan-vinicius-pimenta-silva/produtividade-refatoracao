using System.ComponentModel.DataAnnotations;

namespace Api.Dtos.Productivity;

public class ServiceOrderCreateDto
{
    [Required]
    public int CompanyId { get; set; }

    public int? ActivityId { get; set; }
    public int? FiscalUserId { get; set; }
    public int? ChiefUserId { get; set; }

    [Required]
    [MaxLength(500)]
    public string Description { get; set; } = string.Empty;

    [MaxLength(500)]
    public string? Observation { get; set; }

    [MaxLength(80)]
    public string? Rc { get; set; }

    [MaxLength(120)]
    public string? DocumentNumber { get; set; }

    [MaxLength(120)]
    public string? ProtocolNumber { get; set; }

    public DateTime? DueDate { get; set; }
    public DateTime? CompletionDate { get; set; }
}

public class ServiceOrderUpdateDto : ServiceOrderCreateDto
{
    public bool IsResponded { get; set; }
    public bool Validated { get; set; }
    public bool Excluded { get; set; }
}

public class ServiceOrderReadDto
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
}
