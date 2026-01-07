using Api.Models;
using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Metadata.Builders;

namespace Api.Data.Configurations;

public class ServiceOrderConfiguration : IEntityTypeConfiguration<ServiceOrder>
{
    public void Configure(EntityTypeBuilder<ServiceOrder> builder)
    {
        builder.ToTable("service_orders");
        builder.HasKey(so => so.Id);
        builder.Property(so => so.Description).IsRequired().HasMaxLength(500);
        builder.Property(so => so.Observation).HasMaxLength(500);
        builder.Property(so => so.Rc).HasMaxLength(80);
        builder.Property(so => so.DocumentNumber).HasMaxLength(120);
        builder.Property(so => so.ProtocolNumber).HasMaxLength(120);
        builder.Property(so => so.CreatedAt).IsRequired();
        builder.Property(so => so.UpdatedAt).IsRequired();

        builder.HasOne(so => so.Company)
            .WithMany(c => c.ServiceOrders)
            .HasForeignKey(so => so.CompanyId)
            .OnDelete(DeleteBehavior.Restrict);

        builder.HasOne(so => so.Activity)
            .WithMany(a => a.ServiceOrders)
            .HasForeignKey(so => so.ActivityId)
            .OnDelete(DeleteBehavior.Restrict);

        builder.HasOne(so => so.FiscalUser)
            .WithMany()
            .HasForeignKey(so => so.FiscalUserId)
            .OnDelete(DeleteBehavior.Restrict);

        builder.HasOne(so => so.ChiefUser)
            .WithMany()
            .HasForeignKey(so => so.ChiefUserId)
            .OnDelete(DeleteBehavior.Restrict);
    }
}
