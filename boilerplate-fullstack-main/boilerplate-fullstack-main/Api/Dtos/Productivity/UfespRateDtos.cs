using System.ComponentModel.DataAnnotations;

namespace Api.Dtos.Productivity;

public class UfespRateCreateDto
{
    [Required]
    public int Year { get; set; }

    [Required]
    public int Value { get; set; }

    public bool Active { get; set; } = true;
}

public class UfespRateUpdateDto : UfespRateCreateDto
{
}

public class UfespRateReadDto
{
    public int Id { get; set; }
    public int Year { get; set; }
    public int Value { get; set; }
    public bool Active { get; set; }
}
