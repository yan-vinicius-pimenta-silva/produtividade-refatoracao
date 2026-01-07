using Api.Models;
using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Metadata.Builders;

namespace Api.Data.Configurations;

public class ActivityConfiguration : IEntityTypeConfiguration<Activity>
{
    public void Configure(EntityTypeBuilder<Activity> builder)
    {
        builder.ToTable("activities");
        builder.HasKey(a => a.Id);
        builder.Property(a => a.Name).IsRequired().HasMaxLength(200);
        builder.Property(a => a.PointsBase).IsRequired();
        builder.Property(a => a.Active).HasDefaultValue(true);
        builder.Property(a => a.Deleted).HasDefaultValue(false);
        builder.Property(a => a.CreatedAt).IsRequired();
        builder.Property(a => a.UpdatedAt).IsRequired();

        builder.HasOne(a => a.Company)
            .WithMany(c => c.Activities)
            .HasForeignKey(a => a.CompanyId)
            .OnDelete(DeleteBehavior.Restrict);

        builder.HasOne(a => a.ActivityType)
            .WithMany(t => t.Activities)
            .HasForeignKey(a => a.ActivityTypeId)
            .OnDelete(DeleteBehavior.Restrict);
    }
}
