using Microsoft.EntityFrameworkCore;
using Api.Models;
using System.Reflection;

namespace Api.Data
{
  public class ApiDbContext : DbContext
  {
    public ApiDbContext(DbContextOptions<ApiDbContext> options) : base(options) { }

    public DbSet<User> Users { get; set; } = null!;
    public DbSet<SystemResource> SystemResources { get; set; } = null!;
    public DbSet<AccessPermission> AccessPermissions { get; set; } = null!;
    public DbSet<SystemLog> SystemLogs { get; set; } = null!;
    public DbSet<Company> Companies { get; set; } = null!;
    public DbSet<ActivityType> ActivityTypes { get; set; } = null!;
    public DbSet<Activity> Activities { get; set; } = null!;
    public DbSet<FiscalActivity> FiscalActivities { get; set; } = null!;
    public DbSet<UfespRate> UfespRates { get; set; } = null!;
    public DbSet<ServiceOrder> ServiceOrders { get; set; } = null!;
    public DbSet<ServiceOrderHistory> ServiceOrderHistories { get; set; } = null!;

    protected override void OnModelCreating(ModelBuilder modelBuilder)
    {
      base.OnModelCreating(modelBuilder);

      // Aplica configurações de IEntityTypeConfiguration<T>
      modelBuilder.ApplyConfigurationsFromAssembly(Assembly.GetExecutingAssembly());
    }
  }
}
