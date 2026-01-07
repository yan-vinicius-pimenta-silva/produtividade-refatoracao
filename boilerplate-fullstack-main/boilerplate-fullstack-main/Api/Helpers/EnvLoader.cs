namespace Api.Helpers;

public static class EnvLoader
{
    public static string GetEnv(string key)
    {
        var value = Environment.GetEnvironmentVariable(key);
        if (string.IsNullOrWhiteSpace(value))
            throw new InvalidOperationException($"Variável de ambiente '{key}' não configurada.");
        return value;
    }

    public static string GetEnv(string key, string defaultValue)
    {
        var value = Environment.GetEnvironmentVariable(key);
        return string.IsNullOrWhiteSpace(value) ? defaultValue : value;
    }
}
