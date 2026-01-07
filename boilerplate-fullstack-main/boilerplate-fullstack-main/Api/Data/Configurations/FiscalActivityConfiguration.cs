using Api.Models;
using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Metadata.Builders;

namespace Api.Data.Configurations;

public class FiscalActivityConfiguration : IEntityTypeConfiguration<FiscalActivity>
{
    public void Configure(EntityTypeBuilder<FiscalActivity> builder)
    {
        builder.ToTable("fiscal_activities");
        builder.HasKey(fa => fa.Id);
        builder.Property(fa => fa.DocumentNumber).HasMaxLength(120);
        builder.Property(fa => fa.ProtocolNumber).HasMaxLength(120);
        builder.Property(fa => fa.Rc).HasMaxLength(80);
        builder.Property(fa => fa.CpfCnpj).HasMaxLength(40);
        builder.Property(fa => fa.Value).HasColumnType("decimal(18,2)");
        builder.Property(fa => fa.Observation).HasMaxLength(500);
        builder.Property(fa => fa.CreatedAt).IsRequired();
        builder.Property(fa => fa.UpdatedAt).IsRequired();
        builder.Property(fa => fa.CompletionDate).IsRequired();

        builder.HasOne(fa => fa.Activity)
            .WithMany(a => a.FiscalActivities)
            .HasForeignKey(fa => fa.ActivityId)
            .OnDelete(DeleteBehavior.Restrict);

        builder.HasOne(fa => fa.Company)
            .WithMany(c => c.FiscalActivities)
            .HasForeignKey(fa => fa.CompanyId)
            .OnDelete(DeleteBehavior.Restrict);

        builder.HasOne(fa => fa.FiscalUser)
            .WithMany()
            .HasForeignKey(fa => fa.FiscalUserId)
            .OnDelete(DeleteBehavior.Restrict);
    }
}
