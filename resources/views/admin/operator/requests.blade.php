@extends('admin.layouts.app-operator')

@section('page_title', 'Mes demandes')
@section('page_subtitle', 'Gestion des ventes opérateur')

@section('content')

<div class="card">

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Produit</th>
                <th>Client</th>
                <th>Statut</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            @foreach($requests as $request)
                <tr>
                    <td>#{{ $request->id }}</td>

                    <td>
                        {{ $request->product->name ?? 'N/A' }}
                    </td>

                    <td>
                        <strong>{{ $request->client_name }}</strong><br>
                        <small style="color:#6b7280;">
                            {{ $request->client_contact }}
                        </small>
                    </td>

                    <td>
                        @if($request->status === 'pending')
                            <span class="badge muted">En attente</span>
                        @elseif($request->status === 'processing')
                            <span class="badge muted">En cours</span>
                        @elseif($request->status === 'available')
                            <span class="badge success">Disponible</span>
                        @elseif($request->status === 'paid')
                            <span class="badge success">Payé</span>
                        @elseif($request->status === 'delivered')
                            <span class="badge success">Livré</span>
                        @elseif($request->status === 'not_available')
                            <span class="badge warning">Non disponible</span>
                        @endif
                    </td>

                    <td>
                        {{ $request->created_at->format('Y-m-d H:i') }}
                    </td>

                    <td>
                        <div class="table-actions">
                            <a href="{{ route('operator.requests.show', $request->id) }}"
                                class="action-btn edit">
                                <i class="fas fa-eye"></i>
                            </a>

                            @if($request->status !== 'paid' 
                                && $request->status !== 'delivered' 
                                && $request->status !== 'not_available')
                                <form method="POST" action="{{ url('operator/requests/'.$request->id.'/pay') }}" style="display:inline;">
                                    @csrf
                                    <button class="action-btn success-action">
                                        <i class="fa-solid fa-coins"></i>
                                    </button>
                                </form>
                            @endif
                            
                            <form action="{{ route('operator.requests.destroy', $request->id) }}" 
                                    method="POST">
                                @csrf
                                @method('DELETE')

                                <button type="submit" 
                                        class="action-btn delete"
                                        onclick="return confirm('Supprimer cette demande ?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                        
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="navigator-container">
        @if ($requests->currentPage() > 1)
            <a href="{{$requests->previousPageUrl()}}"><i class="{{(app()->getLocale() === 'ar') ? 'fa-solid fa-angle-right' : 'fa-solid fa-angle-left'}}"></i></a>
        @else
            <a href="{{$requests->url($requests->currentPage())}}"><i class="{{(app()->getLocale() === 'ar') ? 'fa-solid fa-angle-right' : 'fa-solid fa-angle-left'}}"></i></a>
        @endif

        @for ($i = 1; $i <= $requests->lastPage(); $i++)
            @if ($requests->currentPage() == $i)
                <a href="{{$requests->url($i)}}" class="active">{{$i}}</a>
            @else
                <a href="{{$requests->url($i)}}">{{$i}}</a>
            @endif
        @endfor

        @if ($requests->currentPage() < $requests->lastPage())
            <a href="{{$requests->nextPageUrl()}}"><i class="{{(app()->getLocale() === 'ar') ? 'fa-solid fa-angle-left' : 'fa-solid fa-angle-right'}}"></i></a>
        @else
            <a href="{{$requests->url($requests->currentPage())}}"><i class="{{(app()->getLocale() === 'ar') ? 'fa-solid fa-angle-left' : 'fa-solid fa-angle-right'}}"></i></a>
        @endif
    </div>

</div>

@endsection

