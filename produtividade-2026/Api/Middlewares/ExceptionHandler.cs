using System.Net;
using System.Text.Json;

namespace Api.Middlewares
{
  public class ExceptionHandler
  {
    private readonly RequestDelegate _next;
    private readonly ILogger<ExceptionHandler> _logger;
    private readonly IWebHostEnvironment _environment;

    public ExceptionHandler(RequestDelegate next, ILogger<ExceptionHandler> logger, IWebHostEnvironment environment)
    {
      _next = next;
      _logger = logger;
      _environment = environment;
    }

    public async Task InvokeAsync(HttpContext context)
    {
      try
      {
        await _next(context);
      }
      catch (AppException ex)
      {
        context.Response.StatusCode = ex.StatusCode;
        context.Response.ContentType = "application/json";

        var response = new
        {
          title = ex.Message,
          traceId = context.TraceIdentifier
        };
        var result = JsonSerializer.Serialize(response);
        await context.Response.WriteAsync(result);
      }
      catch (Exception ex)
      {
        _logger.LogError(ex, "Unhandled exception for {Method} {Path} (TraceId: {TraceId})",
          context.Request.Method,
          context.Request.Path,
          context.TraceIdentifier);

        context.Response.StatusCode = (int)HttpStatusCode.InternalServerError;
        context.Response.ContentType = "application/json";

        var response = new
        {
          title = "Ocorreu um erro inesperado.",
          detail = _environment.IsDevelopment() ? ex.Message : null,
          traceId = context.TraceIdentifier
        };
        var result = JsonSerializer.Serialize(response);
        await context.Response.WriteAsync(result);
      }
    }
  }

  // Extensão para Program.cs
  public static class ExceptionHandlerExtensions
  {
    public static IApplicationBuilder UseExceptionHandlerMiddleware(this IApplicationBuilder app)
    {
      return app.UseMiddleware<ExceptionHandler>();
    }
  }
}
