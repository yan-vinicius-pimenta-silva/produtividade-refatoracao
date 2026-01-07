using System.ComponentModel.DataAnnotations;

namespace Api.Dtos.Productivity;

public class ActivityTypeCreateDto
{
    [Required]
    [MaxLength(80)]
    public string Name { get; set; } = string.Empty;
}

public class ActivityTypeUpdateDto : ActivityTypeCreateDto
{
    public bool Active { get; set; } = true;
}

public class ActivityTypeReadDto
{
    public int Id { get; set; }
    public string Name { get; set; } = string.Empty;
    public bool Active { get; set; }
}
