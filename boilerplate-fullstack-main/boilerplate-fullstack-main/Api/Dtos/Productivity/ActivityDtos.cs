using System.ComponentModel.DataAnnotations;

namespace Api.Dtos.Productivity;

public class ActivityCreateDto
{
    [Required]
    public int CompanyId { get; set; }

    [Required]
    public int ActivityTypeId { get; set; }

    [Required]
    [MaxLength(200)]
    public string Name { get; set; } = string.Empty;

    [Required]
    public int PointsBase { get; set; }
}

public class ActivityUpdateDto : ActivityCreateDto
{
    public bool Active { get; set; } = true;
    public bool Deleted { get; set; } = false;
}

public class ActivityReadDto
{
    public int Id { get; set; }
    public int CompanyId { get; set; }
    public int ActivityTypeId { get; set; }
    public string Name { get; set; } = string.Empty;
    public int PointsBase { get; set; }
    public bool Active { get; set; }
    public bool Deleted { get; set; }
}
