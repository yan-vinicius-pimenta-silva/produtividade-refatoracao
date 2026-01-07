using System.ComponentModel.DataAnnotations;

namespace Api.Dtos.Productivity;

public class CompanyCreateDto
{
    [Required]
    [MaxLength(200)]
    public string Name { get; set; } = string.Empty;

    [MaxLength(200)]
    public string? Email { get; set; }

    [MaxLength(200)]
    public string? Secretary { get; set; }

    [MaxLength(200)]
    public string? Division { get; set; }

    [MaxLength(40)]
    public string? Phone { get; set; }

    [MaxLength(400)]
    public string? LogoUrl { get; set; }

    public string? ParametersJson { get; set; }
}

public class CompanyUpdateDto : CompanyCreateDto
{
    public bool Active { get; set; } = true;
    public bool Deleted { get; set; } = false;
}

public class CompanyReadDto
{
    public int Id { get; set; }
    public string Name { get; set; } = string.Empty;
    public string? Email { get; set; }
    public string? Secretary { get; set; }
    public string? Division { get; set; }
    public string? Phone { get; set; }
    public string? LogoUrl { get; set; }
    public string? ParametersJson { get; set; }
    public bool Active { get; set; }
    public bool Deleted { get; set; }
}
