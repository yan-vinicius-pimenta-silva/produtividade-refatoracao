using Microsoft.EntityFrameworkCore;
using Produtividade.Api.Models;

namespace Produtividade.Api.Data;

public class ProdutividadeDbContext : DbContext
{
    public ProdutividadeDbContext(DbContextOptions<ProdutividadeDbContext> options)
        : base(options)
    {
    }

    public DbSet<Company> Companies => Set<Company>();
    public DbSet<User> Users => Set<User>();
    public DbSet<ActivityType> ActivityTypes => Set<ActivityType>();
    public DbSet<Activity> Activities => Set<Activity>();
    public DbSet<UfespRate> UfespRates => Set<UfespRate>();
    public DbSet<FiscalActivity> FiscalActivities => Set<FiscalActivity>();
    public DbSet<FiscalActivityAttachment> FiscalActivityAttachments => Set<FiscalActivityAttachment>();
    public DbSet<FiscalActivityLedger> FiscalActivityLedgers => Set<FiscalActivityLedger>();
    public DbSet<FiscalTotalPoints> FiscalTotalPoints => Set<FiscalTotalPoints>();
    public DbSet<FiscalPointBank> FiscalPointBanks => Set<FiscalPointBank>();

    protected override void OnModelCreating(ModelBuilder modelBuilder)
    {
        modelBuilder.Entity<UfespRate>()
            .HasKey(rate => rate.Year);

        modelBuilder.Entity<FiscalActivityLedger>()
            .HasKey(ledger => new { ledger.ActivityId, ledger.FiscalId, ledger.EffectiveDate });

        modelBuilder.Entity<FiscalTotalPoints>()
            .HasKey(total => new { total.FiscalId, total.EffectiveDate });

        modelBuilder.Entity<FiscalPointBank>()
            .HasKey(bank => new { bank.FiscalId, bank.EffectiveDate });
    }
}
