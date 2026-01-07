using Api.Models;
using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Metadata.Builders;

namespace Api.Data.Configurations;

public class ActivityTypeConfiguration : IEntityTypeConfiguration<ActivityType>
{
    public void Configure(EntityTypeBuilder<ActivityType> builder)
    {
        builder.ToTable("activity_types");
        builder.HasKey(at => at.Id);
        builder.Property(at => at.Name).IsRequired().HasMaxLength(80);
        builder.Property(at => at.Active).HasDefaultValue(true);
        builder.Property(at => at.CreatedAt).IsRequired();
        builder.Property(at => at.UpdatedAt).IsRequired();
    }
}
