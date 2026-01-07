using Api.Models;
using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Metadata.Builders;

namespace Api.Data.Configurations;

public class ServiceOrderHistoryConfiguration : IEntityTypeConfiguration<ServiceOrderHistory>
{
    public void Configure(EntityTypeBuilder<ServiceOrderHistory> builder)
    {
        builder.ToTable("service_order_history");
        builder.HasKey(h => h.Id);
        builder.Property(h => h.Status).IsRequired().HasMaxLength(40);
        builder.Property(h => h.StatusColor).HasMaxLength(40);
        builder.Property(h => h.Observation).HasMaxLength(500);
        builder.Property(h => h.AttachmentPath).HasMaxLength(500);
        builder.Property(h => h.CreatedAt).IsRequired();
        builder.Property(h => h.UpdatedAt).IsRequired();

        builder.HasOne(h => h.ServiceOrder)
            .WithMany(so => so.History)
            .HasForeignKey(h => h.ServiceOrderId)
            .OnDelete(DeleteBehavior.Cascade);

        builder.HasOne(h => h.User)
            .WithMany()
            .HasForeignKey(h => h.UserId)
            .OnDelete(DeleteBehavior.Restrict);
    }
}
