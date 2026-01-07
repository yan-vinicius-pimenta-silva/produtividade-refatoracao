using Api.Models;
using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Metadata.Builders;

namespace Api.Data.Configurations;

public class CompanyConfiguration : IEntityTypeConfiguration<Company>
{
    public void Configure(EntityTypeBuilder<Company> builder)
    {
        builder.ToTable("companies");
        builder.HasKey(c => c.Id);
        builder.Property(c => c.Name).IsRequired().HasMaxLength(200);
        builder.Property(c => c.Email).HasMaxLength(200);
        builder.Property(c => c.Secretary).HasMaxLength(200);
        builder.Property(c => c.Division).HasMaxLength(200);
        builder.Property(c => c.Phone).HasMaxLength(40);
        builder.Property(c => c.LogoUrl).HasMaxLength(400);
        builder.Property(c => c.ParametersJson).HasColumnType("text");
        builder.Property(c => c.Active).HasDefaultValue(true);
        builder.Property(c => c.Deleted).HasDefaultValue(false);
        builder.Property(c => c.CreatedAt).IsRequired();
        builder.Property(c => c.UpdatedAt).IsRequired();
    }
}
