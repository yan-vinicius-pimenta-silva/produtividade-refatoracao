using Api.Models;
using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Metadata.Builders;

namespace Api.Data.Configurations;

public class UfespRateConfiguration : IEntityTypeConfiguration<UfespRate>
{
    public void Configure(EntityTypeBuilder<UfespRate> builder)
    {
        builder.ToTable("ufesp_rates");
        builder.HasKey(r => r.Id);
        builder.Property(r => r.Year).IsRequired();
        builder.Property(r => r.Value).IsRequired();
        builder.Property(r => r.Active).HasDefaultValue(true);
        builder.Property(r => r.CreatedAt).IsRequired();
        builder.Property(r => r.UpdatedAt).IsRequired();
        builder.HasIndex(r => r.Year).IsUnique();
    }
}
